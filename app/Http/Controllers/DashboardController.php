<?php

namespace App\Http\Controllers;

use App\Models\EntityType;
use App\Models\Attribute;
use App\Models\Entity;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $entityTypes = EntityType::with(['attributes'])
            ->orderBy('type_name')
            ->get();

        $attributes = Attribute::with(['entityType'])
            ->orderBy('attribute_label')
            ->get();

        $entities = Entity::with(['entityType', 'parent'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('Dashboard', [
            'entityTypes' => $entityTypes,
            'attributes' => $attributes,
            'entities' => $entities
        ]);
    }
}
