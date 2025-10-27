@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $attributeGroup->group_name }}</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Attribute Group Details
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('attribute-groups.edit', $attributeGroup->group_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Group
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Group Information -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Group Information</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Group Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $attributeGroup->group_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Group Code</dt>
                                    <dd class="text-sm text-gray-900 font-mono">{{ $attributeGroup->group_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Entity Type</dt>
                                    <dd class="text-sm text-gray-900">{{ $attributeGroup->entityType->type_name ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                                    <dd class="text-sm text-gray-900">{{ $attributeGroup->sort_order }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm">
                                        @if($attributeGroup->is_active)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="text-sm text-gray-900">{{ $attributeGroup->created_at ? $attributeGroup->created_at->format('M d, Y') : 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Attributes -->
                    <div class="lg:col-span-2">
                        <div class="bg-white border border-gray-200 rounded-lg">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Attributes in this Group</h3>
                                <p class="text-sm text-gray-500">{{ $attributeGroup->attributes->count() }} attributes</p>
                            </div>
                            
                            @if($attributeGroup->attributes->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attribute</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($attributeGroup->attributes as $attribute)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $attribute->attribute_label }}</div>
                                                        <div class="text-sm text-gray-500 font-mono">{{ $attribute->attribute_code }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            {{ ucfirst($attribute->frontend_input) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if($attribute->is_required)
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                                Required
                                                            </span>
                                                        @else
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                                Optional
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ $attribute->sort_order }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('attributes.show', $attribute->attribute_id) }}" class="text-indigo-600 hover:text-indigo-500">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="px-4 py-8 text-center text-gray-500">
                                    <svg class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 010-3.586l.653-.653a2.548 2.548 0 013.586 0l5.653 4.655" />
                                    </svg>
                                    <p class="text-sm">No attributes assigned to this group yet.</p>
                                    <a href="{{ route('attributes.create', ['group_id' => $attributeGroup->group_id]) }}" class="text-indigo-600 hover:text-indigo-500 text-sm mt-2 inline-block">
                                        Create an attribute for this group
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
