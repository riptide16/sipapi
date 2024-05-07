<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\InstrumentComponentCollection;
use App\Http\Resources\InstrumentComponentResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\Resource;
use App\Models\InstrumentComponent;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InstrumentComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $components = InstrumentComponent::with('parent.parent')->filter($request->all());
        $components = $request->has('per_page') && $request->per_page <= -1
            ? $components->get()
            : $components->paginate($request->per_page ?? 20)->withQueryString();

        return new InstrumentComponentCollection($components);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:191',
            'category' => ['required', Rule::in(InstrumentComponent::categoryList())],
            'weight' => [
                'required_if:type,' . InstrumentComponent::TYPE_MAIN,
                'numeric',
                'min:0',
            ],
            'type' => ['required', Rule::in(InstrumentComponent::typeList())],
            'order' => 'required|numeric|min:1',
            'parent_id' => [
                'prohibited_if:type,' .  InstrumentComponent::TYPE_MAIN,
                'required_unless:type,' . InstrumentComponent::TYPE_MAIN,
                'exists:instrument_components,id',
            ],

        ]);

        if ($request->has('parent_id')) {
            $parent = InstrumentComponent::find($request->parent_id);
            if (!$parent->canBeAttachedWith($request->type)) {
                $msg = [
                    'parent_id' => [
                        'Parent can only be attached to correct children.'
                    ]
                ];
                return new ErrorResource($msg, 422, 'ERR4022');
            }
        }

        $this->validateComponentExists(
            $request->get('category'),
            $request->get('name'), 
            $request->get('parent_id')
        );

        $component = InstrumentComponent::create($request->all());
        $component->loadParents();

        return new InstrumentComponentResource($component, 201);
    }

    protected function validateComponentExists($category, $name, $parentId = null, $componentId = null)
    {
        $wheres = [
            'category' => $category,
            'name' => $name,
        ];
        if ($parentId) {
            $wheres['parent_id'] = $parentId;
        }

        $component = InstrumentComponent::where($wheres)->first();
        if ($component && (!$componentId || $componentId !== $component->id)) {
            $msg = [
                'name' => [
                    'Instrument already exists.'
                ]
            ];
            throw ValidationException::withMessages($msg);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $component = InstrumentComponent::findOrFail($id);
        $component->loadParents();

        return new InstrumentComponentResource($component);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $component = InstrumentComponent::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|max:191',
            'category' => [
                'sometimes',
                Rule::in(InstrumentComponent::categoryList()),
            ],
            'weight' => 'sometimes|numeric|min:0',
            'order' => 'sometimes|numeric|min:1',
            'parent_id' => 'sometimes|exists:instrument_components,id',
        ]);
        $data = $request->all();

        if ($request->has('parent_id')) {
            $parent = InstrumentComponent::find($request->parent_id);
            if (!$parent->canBeAttachedWith($component->type)) {
                $msg = [
                    'parent_id' => [
                        'Parent can only be attached to correct children.'
                    ]
                ];
                return new ErrorResource($msg, 422, 'ERR4022');
            }
        }

        $this->validateComponentExists(
            $request->get('category'),
            $request->get('name'), 
            $request->get('parent_id'),
            $component->id
        );

        unset($data['type']);

        $component->update($data);

        if ($component->isMain() && $request->has('category')) {
            $component->children()->update(['category' => $component->category]);
            foreach ($component->children as $child) {
                $child->children()->update(['category' => $component->category]);
            }
        }

        $component->loadParents();

        return new InstrumentComponentResource($component);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $component = InstrumentComponent::with('children')->findOrFail($id);
        if (!$component->children->isEmpty()) {
            return new ErrorResource(__('errors.constraint_violation'), 406, 'ERR4506');
        }
        $component->delete();

        return new Resource();
    }
}
