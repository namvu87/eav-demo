<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $frontendInput = $this->input('frontend_input');

        return [
            'entity_type_id' => 'nullable|exists:entity_types,entity_type_id',
            'attribute_code' => 'required|string|max:100|unique:attributes,attribute_code',
            'attribute_label' => 'required|string|max:255',
            'backend_type' => 'required|in:varchar,text,int,decimal,datetime,file',
            'frontend_input' => 'required|in:text,textarea,select,multiselect,radio,checkbox,file,date,datetime,number',
            'is_required' => 'sometimes|boolean',
            'is_unique' => 'sometimes|boolean',
            'is_searchable' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
            'default_value' => 'nullable|string',
            'options' => $this->optionsRequired($frontendInput) ? 'required|array|min:1' : 'nullable|array',
            'options.*.option_value' => 'required_with:options|string|max:255',
            'options.*.option_label' => 'required_with:options|string|max:255',
            'options.*.sort_order' => 'sometimes|integer|min:0',
        ];
    }

    /**
     * Check if options are required based on frontend input type
     */
    protected function optionsRequired($frontendInput): bool
    {
        return in_array($frontendInput, ['select', 'multiselect', 'radio', 'checkbox']);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'attribute_code.required' => 'Mã thuộc tính không được để trống.',
            'attribute_code.unique' => 'Mã thuộc tính đã tồn tại.',
            'attribute_code.max' => 'Mã thuộc tính không được vượt quá 100 ký tự.',
            'attribute_label.required' => 'Tên hiển thị không được để trống.',
            'attribute_label.max' => 'Tên hiển thị không được vượt quá 255 ký tự.',
            'backend_type.required' => 'Kiểu dữ liệu không được để trống.',
            'backend_type.in' => 'Kiểu dữ liệu không hợp lệ.',
            'frontend_input.required' => 'Kiểu nhập liệu không được để trống.',
            'frontend_input.in' => 'Kiểu nhập liệu không hợp lệ.',
            'entity_type_id.exists' => 'Loại thực thể không tồn tại.',
            'options.required' => 'Tùy chọn là bắt buộc cho kiểu nhập liệu này.',
            'options.min' => 'Phải có ít nhất 1 tùy chọn.',
            'options.*.option_value.required' => 'Giá trị tùy chọn không được để trống.',
            'options.*.option_label.required' => 'Nhãn tùy chọn không được để trống.',
        ];
    }
}
