import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import EntityTypesView from './EntityTypesView';
import AttributesView from './AttributesView';
import EntitiesView from './EntitiesView';
import RelationsView from './RelationsView';
import SearchView from './SearchView';
import { Database, Boxes, FileText, Link, Search } from 'lucide-react';

export default function Index() {
    const [currentView, setCurrentView] = useState('types');

    const navigation = [
        { id: 'types', label: 'Entity Types', icon: Database, component: EntityTypesView },
        { id: 'attributes', label: 'Attributes', icon: Boxes, component: AttributesView },
        { id: 'entities', label: 'Entities', icon: FileText, component: EntitiesView },
        { id: 'relations', label: 'Relations', icon: Link, component: RelationsView },
        { id: 'search', label: 'Search', icon: Search, component: SearchView }
    ];

    const CurrentComponent = navigation.find(n => n.id === currentView)?.component || EntityTypesView;

    return (
        <>
            <Head title="EAV Management" />

            <div className="min-h-screen bg-gray-50">
                {/* Navigation giống trong artifact */}
                <nav className="bg-white shadow-sm border-b sticky top-0 z-40">
                    {/* Copy navigation code từ artifact */}
                </nav>

                <main className="max-w-7xl mx-auto">
                    <CurrentComponent />
                </main>
            </div>
        </>
    );
}
