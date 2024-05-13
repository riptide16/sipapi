<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccreditationCollection;
use App\Http\Resources\AccreditationResource;
use App\Http\Resources\EvaluationCollection;
use App\Http\Resources\EvaluationAssignmentCollection;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\InstitutionResource;
use App\Http\Requests\Admin\SubmitEvaluationRequest;
use App\Events\AccreditationEvaluated;
use App\Models\Accreditation;
use App\Models\AccreditationContent;
use App\Models\Evaluation;
use App\Models\EvaluationAssignment;
use App\Models\EvaluationContent;
use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use App\Models\InstrumentComponent;
use App\Models\Institution;
use Illuminate\Http\Request;

use Storage;
use PDF;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Evaluations\EvaluationContentExport;
use App\Exports\Evaluations\OnthespotExport;


class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $accreditations = Accreditation::with(['institution', 'evaluation'])
            ->canBeEvaluated($user->id)
            ->orderBy('id', 'desc')
            ->filter($request->all());

        $accreditations = $request->has('per_page') && $request->per_page <= -1
            ? $accreditations->get()
            : $accreditations->paginate($request->per_page ?? 20)->withQueryString();

        return new AccreditationCollection($accreditations);

        // $evaluations = Evaluation::with(['accreditation', 'institution'])->filter($request->all());
        // $evaluations = $request->has('per_page') && $request->per_page <= -1
        //     ? $evaluations->get()
        //     : $evaluations->paginate($request->per_page ?? 20)->withQueryString();

        // return new EvaluationCollection($evaluations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubmitEvaluationRequest $request)
    {
        $user = $request->user();
        $accreditation = Accreditation::findOrFail($request->accreditation_id);
        $evaluation = Evaluation::firstOrNew([
            'accreditation_id' => $accreditation->id,
            'institution_id' => $accreditation->institution_id,
            'assessor_id' => $user->id,
        ]);

        if (!$evaluation->exists) {
            $evaluation->save();
        }

        $savedContents = [];
        foreach ($request->contents as $i => $content) {
            $data = [
                'evaluation_id' => $evaluation->id,
                'accreditation_content_id' => $content['accreditation_content_id'],
            ];
            $accContent = AccreditationContent::find($content['accreditation_content_id']);
            $point = InstrumentAspectPoint::find($content['instrument_aspect_point_id']);
            if (!$point) {
                return new ErrorResource('Opsi tidak valid untuk data ke-'.($i+1), 422);
            }

            $data['instrument_aspect_point_id'] = $point->id;
            $data['statement'] = $point ? $point->statement : null;
            $data['value'] = $point ? $point->value : null;

            $savedContent = EvaluationContent::updateOrCreate([
                'evaluation_id' => $data['evaluation_id'],
                'accreditation_content_id' => $data['accreditation_content_id'],
            ], $data);
            $savedContents[] = $savedContent;
        }
        
        $evaluation->refresh()->load('contents', 'assessor');

        $isComplete = (bool) $request->is_complete;
        $evaluation->need_upload_document = $isComplete && !$evaluation->document_file;
        if ($isComplete && !$evaluation->need_upload_document) {
            event(new AccreditationEvaluated($accreditation, $evaluation));
        }

        return new EvaluationResource($evaluation, 201);
    }

    public function uploadDocument(Request $request, $id)
    {
        $evaluation = Evaluation::findOrFail($id);

        $request->validate([
            'file' => 'sometimes|file|max:2048|mimes:pdf,doc,docx,xlsx',
            'recommendations' => 'array|sometimes',
            'recommendations.*.instrument_component_id' => 'required_with:recommendations|exists:instrument_components,id',
            'recommendations.*.content' => 'required_with:recommendations',
        ]);

        if ($request->has('file')) {
            $path = $request->file('file')->storeAs("evaluations/{$id}",$request->file('file')->getClientOriginalName());
            if ($evaluation->document_file) {
                Storage::disk('local')->delete($evaluation->document_file);
            }
            $evaluation->document_file = $path;
            $evaluation->save();

            return new EvaluationResource($evaluation);
        }

        if ($request->has('recommendations')) {
            $recommendations = [];
            foreach ($request->get('recommendations') as $recommendation) {
                $component = InstrumentComponent::find($recommendation['instrument_component_id']);
                $recommendations[] = [
                    'component_id' => $component->id,
                    'name' => $component->name,
                    'content' => $recommendation['content'],
                ];
            }
            $evaluation->recommendations = $recommendations;
        }

        $evaluation->save();

        event(new AccreditationEvaluated($evaluation->accreditation, $evaluation));

        return new EvaluationResource($evaluation);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $evaluation = Evaluation::with(['accreditation', 'contents', 'institution'])
                                ->findOrFail($id);

        return new EvaluationResource($evaluation);
    }

    /**
     * Get institution data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showInstitution($id)
    {
        $accreditation = Accreditation::findOrFail($id);
	$institutionId = $accreditation->institution_id;
        $institution = Institution::with(['region', 'province', 'city', 'subdistrict', 'village'])->findOrFail($institutionId);
        return new InstitutionResource($institution);
    }

    public function downloadDocument($id)
    {

        $evaluation = Evaluation::with('institution')->findOrFail($id);
        $evaluation->evaluationResult();
        $evaluation->finalResult = $evaluation->finalResult();

        $evaluationResult = $evaluation->evaluationResult;

        usort($evaluationResult, fn($a, $b) => $a['instrument_component_id'] <=> $b['instrument_component_id']);

        $assignment = EvaluationAssignment::with('assessors')
            ->where('accreditation_id', $evaluation->accreditation_id)
            ->first();

        $pdf = PDF::loadView(
            'templates.evaluation-document-pdf',
            compact('evaluation', 'evaluationResult', 'assignment'),
            [],
            [
                'title' => 'Berita Acara',
            ]
        );

        $fullPath = 'evaluations/berita-acara/'.$id.'.pdf';
        $moveToStorage = Storage::put($fullPath, $pdf->output());
        $storage = Storage::disk('local');
        if ($storage->exists($fullPath)) {
            return $storage->download($fullPath);
        }

        return false;
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function exportOnthespot(Request $request)
    {
        $id = $request->get('id');

        $accreditationContent = AccreditationContent::where('accreditation_id',$id)->where('type','choice')->get();

        $evaluationContentTest = [];

        // foreach($accreditationContent as $row){
        //     $answer = DB::table('evaluation_contents AS e')
        //                     ->join('accreditation_contents AS a','a.id','e.accreditation_content_id')
        //                     ->where('instrument_aspect_point_id', $row['instrument_aspect_point_id'])
        //                     ->orderBy('created_at','desc')
        //                     ->get();
        //     // array_push($evaluationContentTest, $answer);
        // }
        
        $evaluationContent = DB::table('evaluation_contents AS e')
                ->leftJoin('evaluations AS ev', 'ev.id', 'e.evaluation_id')
                ->leftJoin('accreditation_contents AS a', 'a.id', 'e.accreditation_content_id')
                ->leftJoin('instrument_components AS i', 'i.id', 'a.main_component_id')
                ->select('e.statement','i.name','e.value','e.evaluation_id')
                ->where('a.accreditation_id',$id)
                ->where('a.accreditation_id',$id)
                ->get();

        $component = DB::table('evaluation_contents AS e')
                    ->leftJoin('accreditation_contents AS a', 'a.id', 'e.accreditation_content_id')
                    ->leftJoin('instrument_components AS i', 'i.id', 'a.main_component_id')
                    ->select('i.name')
                    ->where('i.category',"Perguruan Tinggi")
                    ->orderBy('i.order','ASC')
                    ->distinct()
                    ->get();

        // Hasil Akreditasi
        $with = ['institution', 'accreditation'];
        $evaluation = Evaluation::with($with);
        $evaluation = $evaluation->findOrFail($evaluationContent[0]->evaluation_id);
        $evaluation->loadResult();

        $accreditationResult = $evaluation['result'];
        usort($accreditationResult, fn($a, $b) => $a['instrument_component_id'] <=> $b['instrument_component_id']);

        // Rekomendasi
        $recommendations = $evaluation['recommendations'];
        $index = 0;

        foreach($accreditationResult as $result){
            $recommendations[$index]['weight'] = $result['weight'];
            $recommendations[$index]['score'] = $result['score'];
            $recommendations[$index]['percentage'] = $result['score']/$result['weight']*100;
            $index++;
        }

        // Asesor yang Bertugas
        $assignments = EvaluationAssignment::with('assessors')
            ->where('accreditation_id', $id)
            ->get();

        $assignmentData = new EvaluationAssignmentCollection($assignments);

        // Data
        $data = [
            'evaluationContent' => $evaluationContent,
            'accreditationData' => $evaluation, 
            'accreditationResult' => $accreditationResult, 
            'assignmentData' => $assignmentData[0],
            'component' => $component,
            'recommendations' => $recommendations,
            'evaluationContentTest' => $accreditationContent
        ];
        $data['predicate'] = $this->calculatePredicate($data['accreditationData']['finalResult']['score']);

        $today = now()->format('Ymd');
        return Excel::download(new OnthespotExport($data), $today.'_'.$evaluation->institution->library_name.'.xlsx');
    }

    public function calculatePredicate($score)
    {
        if ($score >= 91) {
            return 'A';
        } elseif ($score >= 76) {
            return 'B';
        } elseif ($score >= 60) {
            return 'C';
        } else {
            return 'Tidak Akreditasi';
        }
    }

}

