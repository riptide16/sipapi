<?php

namespace App\Http\Resources;


class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth()->user();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->when($user, $this->username),
            'email' => $this->when($user, $this->email),
            'profile_picture' => $this->profile_picture
                ? config('services.frontend.storage_url').$this->profile_picture
                : null,
            'region' => new RegionResource($this->whenLoaded('region')),
            $this->mergeWhen($user && $user->isAdmin(), [
                'role' => new RoleResource($this->whenLoaded('role')),
                'region' => new RegionResource($this->whenLoaded('region')),
                'status' => $this->status,
                'status_text' => __('models.user.status.'.$this->status),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'email_verified_at' => $this->email_verified_at,
                'institution_name' => $this->institution_name,
                'province' => new ProvinceResource($this->whenLoaded('province')),
            ]),
            $this->mergeWhen($user && $user->id === $this->id, [
                'phone_number' => $this->phone_number,
                'region' => new RegionResource($this->whenLoaded('region')),
                'institution_name' => $this->institution_name,
                'role' => new RoleResource($this->whenLoaded('role')),
                'province' => new ProvinceResource($this->whenLoaded('province')),
            ]),
        ];
    }
}
