@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">EAV Entities</h1>
                <p class="text-gray-600 mb-4">This is the Entities page (not a modal).</p>
                
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h3 class="text-sm font-medium text-blue-800">Debug Information:</h3>
                    <ul class="mt-2 text-sm text-blue-700">
                        <li>Current URL: {{ request()->url() }}</li>
                        <li>Current Route: {{ request()->route()->getName() }}</li>
                        <li>Controller: {{ request()->route()->getActionName() }}</li>
                    </ul>
                </div>
                
                <div class="mt-6">
                    <a href="/eav" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Go to EAV Index
                    </a>
                    <a href="/hierarchy" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Go to Hierarchy
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
