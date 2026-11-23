<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        // "800.000" / "Rp 800000" -> 800000
        $this->merge(['price' => preg_replace('/\D/', '', (string) $this->input('price'))]);
    }

    public function rules(): array
    {
        $room = $this->route('room');

        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('rooms', 'name')->ignore($room?->id)],
            'price' => ['required', 'integer', 'min:1', 'max:100000000'],
            'status' => ['required', Rule::in(['tersedia', 'terisi'])],
            'description' => ['nullable', 'string', 'max:2000'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', Rule::in(array_keys(config('kos.facilities')))],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:60'],
            'delete_photos' => ['nullable', 'array'],
            'delete_photos.*' => ['integer'],
            'cover_photo' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kamar wajib diisi.',
            'name.unique' => 'Nama kamar sudah dipakai.',
            'price.required' => 'Tarif sewa wajib diisi.',
            'price.integer' => 'Tarif sewa harus berupa angka.',
            'price.min' => 'Tarif sewa harus lebih dari 0.',
            'status.in' => 'Status tidak valid.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'photos.max' => 'Maksimal 10 foto sekali unggah.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.mimes' => 'Format foto harus JPG atau PNG.',
            'photos.*.max' => 'Ukuran tiap foto maksimal 5MB.',
            'photos.*.uploaded' => 'Foto gagal diunggah (maksimal 5MB per file).',
        ];
    }
}
