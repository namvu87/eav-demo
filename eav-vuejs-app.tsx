import React, { useState, useEffect } from 'react';
import { AlertCircle, Plus, Edit2, Trash2, Search, ChevronRight, ChevronDown, Filter, ArrowUpDown, Eye, Copy, Move, FileText, Database, Boxes, Link } from 'lucide-react';

// API Configuration
const API_BASE = '/api';

// API Service
const api = {
  async get(url) {
    const res = await fetch(`${API_BASE}${url}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  },
  async post(url, data) {
    const res = await fetch(`${API_BASE}${url}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  },
  async put(url, data) {
    const res = await fetch(`${API_BASE}${url}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  },
  async delete(url) {
    const res = await fetch(`${API_BASE}${url}`, { method: 'DELETE' });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  }
};

// Entity Types Management
const EntityTypesView = () => {
  const [types, setTypes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingType, setEditingType] = useState(null);
  const [formData, setFormData] = useState({
    type_code: '',
    type_name: '',
    type_name_en: '',
    icon: '📦',
    color: '#3b82f6',
    code_prefix: '',
    description: '',
    is_active: true,
    sort_order: 0
  });

  useEffect(() => {
    loadTypes();
  }, []);

  const loadTypes = async () => {
    try {
      const data = await api.get('/entity-types');
      setTypes(data);
    } catch (err) {
      alert('Failed to load entity types');
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingType) {
        await api.put(`/entity-types/${editingType.entity_type_id}`, formData);
      } else {
        await api.post('/entity-types', formData);
      }
      setShowForm(false);
      setEditingType(null);
      resetForm();
      loadTypes();
    } catch (err) {
      alert('Failed to save entity type');
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Are you sure?')) return;
    try {
      await api.delete(`/entity-types/${id}`);
      loadTypes();
    } catch (err) {
      alert('Failed to delete');
    }
  };

  const handleEdit = (type) => {
    setEditingType(type);
    setFormData({
      type_code: type.type_code,
      type_name: type.type_name,
      type_name_en: type.type_name_en || '',
      icon: type.icon || '📦',
      color: type.color || '#3b82f6',
      code_prefix: type.code_prefix || '',
      description: type.description || '',
      is_active: type.is_active,
      sort_order: type.sort_order || 0
    });
    setShowForm(true);
  };

  const resetForm = () => {
    setFormData({
      type_code: '',
      type_name: '',
      type_name_en: '',
      icon: '📦',
      color: '#3b82f6',
      code_prefix: '',
      description: '',
      is_active: true,
      sort_order: 0
    });
  };

  if (loading) return <div className="p-8 text-center">Loading...</div>;

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold flex items-center gap-2">
          <Database className="w-8 h-8" />
          Entity Types
        </h1>
        <button
          onClick={() => { setShowForm(true); setEditingType(null); resetForm(); }}
          className="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700"
        >
          <Plus className="w-4 h-4" /> New Type
        </button>
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <h2 className="text-2xl font-bold mb-4">
              {editingType ? 'Edit' : 'New'} Entity Type
            </h2>
            <form onSubmit={handleSubmit}>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1">Type Code *</label>
                  <input
                    type="text"
                    value={formData.type_code}
                    onChange={(e) => setFormData({...formData, type_code: e.target.value})}
                    className="w-full border rounded px-3 py-2"
                    required
                    pattern="[a-z0-9_]+"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Code Prefix</label>
                  <input
                    type="text"
                    value={formData.code_prefix}
                    onChange={(e) => setFormData({...formData, code_prefix: e.target.value})}
                    className="w-full border rounded px-3 py-2"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Type Name (VI) *</label>
                  <input
                    type="text"
                    value={formData.type_name}
                    onChange={(e) => setFormData({...formData, type_name: e.target.value})}
                    className="w-full border rounded px-3 py-2"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Type Name (EN)</label>
                  <input
                    type="text"
                    value={formData.type_name_en}
                    onChange={(e) => setFormData({...formData, type_name_en: e.target.value})}
                    className="w-full border rounded px-3 py-2"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Icon (Emoji)</label>
                  <input
                    type="text"
                    value={formData.icon}
                    onChange={(e) => setFormData({...formData, icon: e.target.value})}
                    className="w-full border rounded px-3 py-2"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Color</label>
                  <input
                    type="color"
                    value={formData.color}
                    onChange={(e) => setFormData({...formData, color: e.target.value})}
                    className="w-full border rounded px-3 py-2"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Sort Order</label>
                  <input
                    type="number"
                    value={formData.sort_order}
                    onChange={(e) => setFormData({...formData, sort_order: parseInt(e.target.value)})}
                    className="w-full border rounded px-3 py-2"
                  />
                </div>
                <div className="flex items-center">
                  <label className="flex items-center gap-2">
                    <input
                      type="checkbox"
                      checked={formData.is_active}
                      onChange={(e) => setFormData({...formData, is_active: e.target.checked})}
                      className="w-4 h-4"
                    />
                    <span className="text-sm font-medium">Active</span>
                  </label>
                </div>
              </div>
              <div className="mt-4">
                <label className="block text-sm font-medium mb-1">Description</label>
                <textarea
                  value={formData.description}
                  onChange={(e) => setFormData({...formData, description: e.target.value})}
                  className="w-full border rounded px-3 py-2"
                  rows={3}
                />
              </div>
              <div className="flex gap-2 mt-6">
                <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                  Save
                </button>
                <button
                  type="button"
                  onClick={() => { setShowForm(false); setEditingType(null); }}
                  className="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prefix</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {types.map(type => (
              <tr key={type.entity_type_id}>
                <td className="px-6 py-4 text-2xl">{type.icon || '📦'}</td>
                <td className="px-6 py-4">
                  <span className="px-2 py-1 rounded text-sm font-mono" style={{backgroundColor: type.color + '20', color: type.color}}>
                    {type.type_code}
                  </span>
                </td>
                <td className="px-6 py-4 font-medium">{type.type_name}</td>
                <td className="px-6 py-4">
                  <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                    {type.code_prefix || '-'}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <span className={`px-2 py-1 rounded text-sm ${type.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                    {type.is_active ? 'Active' : 'Inactive'}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <div className="flex gap-2">
                    <button
                      onClick={() => handleEdit(type)}
                      className="text-blue-600 hover:text-blue-800"
                    >
                      <Edit2 className="w-4 h-4" />
                    </button>
                    <button
                      onClick={() => handleDelete(type.entity_type_id)}
                      className="text-red-600 hover:text-red-800"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

// Attributes Management
const AttributesView = () => {
  const [attributes, setAttributes] = useState([]);
  const [types, setTypes] = useState([]);
  const [selectedType, setSelectedType] = useState('all');
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [typesData] = await Promise.all([
        api.get('/entity-types')
      ]);
      setTypes(typesData);
      loadAttributes();
    } catch (err) {
      alert('Failed to load data');
    } finally {
      setLoading(false);
    }
  };

  const loadAttributes = async () => {
    try {
      if (selectedType === 'all') {
        const allAttrs = await api.get('/attributes/shared');
        setAttributes(allAttrs);
      } else {
        const attrs = await api.get(`/entity-types/${selectedType}/attributes`);
        setAttributes(attrs);
      }
    } catch (err) {
      console.error(err);
    }
  };

  useEffect(() => {
    if (!loading) loadAttributes();
  }, [selectedType]);

  if (loading) return <div className="p-8 text-center">Loading...</div>;

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold flex items-center gap-2">
          <Boxes className="w-8 h-8" />
          Attributes
        </h1>
        <button
          onClick={() => setShowForm(true)}
          className="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700"
        >
          <Plus className="w-4 h-4" /> New Attribute
        </button>
      </div>

      <div className="mb-4">
        <label className="block text-sm font-medium mb-2">Filter by Entity Type</label>
        <select
          value={selectedType}
          onChange={(e) => setSelectedType(e.target.value)}
          className="border rounded px-3 py-2 w-64"
        >
          <option value="all">All / Shared</option>
          {types.map(type => (
            <option key={type.entity_type_id} value={type.entity_type_id}>
              {type.icon} {type.type_name}
            </option>
          ))}
        </select>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Label</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Input</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Required</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {attributes.map(attr => (
              <tr key={attr.attribute_id}>
                <td className="px-6 py-4 font-mono text-sm">{attr.attribute_code}</td>
                <td className="px-6 py-4 font-medium">{attr.attribute_label}</td>
                <td className="px-6 py-4">
                  <span className="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm">
                    {attr.backend_type}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                    {attr.frontend_input}
                  </span>
                </td>
                <td className="px-6 py-4">
                  {attr.is_required && <span className="text-red-600">★</span>}
                </td>
                <td className="px-6 py-4">
                  <div className="flex gap-2">
                    <button className="text-blue-600 hover:text-blue-800">
                      <Edit2 className="w-4 h-4" />
                    </button>
                    <button className="text-red-600 hover:text-red-800">
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

// Entities Management
const EntitiesView = () => {
  const [entities, setEntities] = useState([]);
  const [types, setTypes] = useState([]);
  const [selectedType, setSelectedType] = useState(null);
  const [loading, setLoading] = useState(true);
  const [expandedNodes, setExpandedNodes] = useState(new Set());

  useEffect(() => {
    loadTypes();
  }, []);

  const loadTypes = async () => {
    try {
      const data = await api.get('/entity-types');
      setTypes(data);
      if (data.length > 0) {
        setSelectedType(data[0].entity_type_id);
      }
    } catch (err) {
      alert('Failed to load types');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (selectedType) loadEntities();
  }, [selectedType]);

  const loadEntities = async () => {
    try {
      const data = await api.get(`/entity-types/${selectedType}/tree`);
      if (data.success) {
        setEntities(data.data);
      }
    } catch (err) {
      console.error(err);
    }
  };

  const toggleNode = (id) => {
    const newExpanded = new Set(expandedNodes);
    if (newExpanded.has(id)) {
      newExpanded.delete(id);
    } else {
      newExpanded.add(id);
    }
    setExpandedNodes(newExpanded);
  };

  const renderTree = (items, parentId = null, level = 0) => {
    return items
      .filter(item => item.parent_id === parentId)
      .map(item => {
        const hasChildren = items.some(i => i.parent_id === item.entity_id);
        const isExpanded = expandedNodes.has(item.entity_id);

        return (
          <div key={item.entity_id}>
            <div
              className="flex items-center gap-2 py-2 px-4 hover:bg-gray-50 cursor-pointer"
              style={{ paddingLeft: `${level * 24 + 16}px` }}
            >
              {hasChildren && (
                <button onClick={() => toggleNode(item.entity_id)} className="w-5 h-5">
                  {isExpanded ? <ChevronDown className="w-4 h-4" /> : <ChevronRight className="w-4 h-4" />}
                </button>
              )}
              {!hasChildren && <span className="w-5" />}
              <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-mono">
                {item.entity_code}
              </span>
              <span className="font-medium">{item.entity_name}</span>
              <span className="text-sm text-gray-500">Level {item.level}</span>
            </div>
            {isExpanded && hasChildren && renderTree(items, item.entity_id, level + 1)}
          </div>
        );
      });
  };

  if (loading) return <div className="p-8 text-center">Loading...</div>;

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold flex items-center gap-2">
          <FileText className="w-8 h-8" />
          Entities
        </h1>
        <button className="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700">
          <Plus className="w-4 h-4" /> New Entity
        </button>
      </div>

      <div className="mb-4">
        <label className="block text-sm font-medium mb-2">Entity Type</label>
        <select
          value={selectedType || ''}
          onChange={(e) => setSelectedType(parseInt(e.target.value))}
          className="border rounded px-3 py-2 w-64"
        >
          {types.map(type => (
            <option key={type.entity_type_id} value={type.entity_type_id}>
              {type.icon} {type.type_name}
            </option>
          ))}
        </select>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        {entities.length === 0 ? (
          <div className="p-8 text-center text-gray-500">No entities found</div>
        ) : (
          <div className="divide-y divide-gray-200">
            {renderTree(entities)}
          </div>
        )}
      </div>
    </div>
  );
};

// Relations Management
const RelationsView = () => {
  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold flex items-center gap-2">
          <Link className="w-8 h-8" />
          Entity Relations
        </h1>
        <button className="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700">
          <Plus className="w-4 h-4" /> New Relation
        </button>
      </div>
      <div className="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        Relations management coming soon...
      </div>
    </div>
  );
};

// Main App
export default function App() {
  const [currentView, setCurrentView] = useState('types');

  const navigation = [
    { id: 'types', label: 'Entity Types', icon: Database, component: EntityTypesView },
    { id: 'attributes', label: 'Attributes', icon: Boxes, component: AttributesView },
    { id: 'entities', label: 'Entities', icon: FileText, component: EntitiesView },
    { id: 'relations', label: 'Relations', icon: Link, component: RelationsView }
  ];

  const CurrentComponent = navigation.find(n => n.id === currentView)?.component || EntityTypesView;

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex items-center justify-between h-16">
            <div className="flex items-center gap-2">
              <Database className="w-8 h-8 text-blue-600" />
              <span className="text-xl font-bold">EAV Management System</span>
            </div>
            <div className="flex gap-1">
              {navigation.map(nav => {
                const Icon = nav.icon;
                return (
                  <button
                    key={nav.id}
                    onClick={() => setCurrentView(nav.id)}
                    className={`px-4 py-2 rounded-lg flex items-center gap-2 transition-colors ${
                      currentView === nav.id
                        ? 'bg-blue-100 text-blue-700 font-medium'
                        : 'text-gray-600 hover:bg-gray-100'
                    }`}
                  >
                    <Icon className="w-4 h-4" />
                    {nav.label}
                  </button>
                );
              })}
            </div>
          </div>
        </div>
      </nav>

      <main className="max-w-7xl mx-auto">
        <CurrentComponent />
      </main>
    </div>
  );
}