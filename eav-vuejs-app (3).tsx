import React, { useState, useEffect } from 'react';
import { AlertCircle, Plus, Edit2, Trash2, Search, ChevronRight, ChevronDown, Filter, ArrowUpDown, Eye, Copy, Move, FileText, Database, Boxes, Link, Save, X, Upload, Settings } from 'lucide-react';

// API Configuration
const API_BASE = '/api';

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

// Alert Component
const Alert = ({ type = 'info', message, onClose }) => {
  const colors = {
    success: 'bg-green-100 border-green-400 text-green-800',
    error: 'bg-red-100 border-red-400 text-red-800',
    info: 'bg-blue-100 border-blue-400 text-blue-800'
  };

  return (
    <div className={`${colors[type]} border-l-4 p-4 mb-4 flex items-center justify-between`}>
      <div className="flex items-center gap-2">
        <AlertCircle className="w-5 h-5" />
        <span>{message}</span>
      </div>
      {onClose && (
        <button onClick={onClose} className="ml-4">
          <X className="w-4 h-4" />
        </button>
      )}
    </div>
  );
};

// Modal Component
const Modal = ({ isOpen, onClose, title, children, size = 'max-w-2xl' }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className={`bg-white rounded-lg ${size} w-full max-h-[90vh] overflow-hidden flex flex-col`}>
        <div className="flex justify-between items-center p-6 border-b">
          <h2 className="text-2xl font-bold">{title}</h2>
          <button onClick={onClose} className="text-gray-500 hover:text-gray-700">
            <X className="w-6 h-6" />
          </button>
        </div>
        <div className="overflow-y-auto p-6">
          {children}
        </div>
      </div>
    </div>
  );
};

// Entity Types Management
const EntityTypesView = () => {
  const [types, setTypes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingType, setEditingType] = useState(null);
  const [alert, setAlert] = useState(null);
  const [formData, setFormData] = useState({
    type_code: '',
    type_name: '',
    type_name_en: '',
    icon: '📦',
    color: '#3b82f6',
    code_prefix: '',
    description: '',
    is_active: true,
    is_system: false,
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
      showAlert('error', 'Failed to load entity types');
    } finally {
      setLoading(false);
    }
  };

  const showAlert = (type, message) => {
    setAlert({ type, message });
    setTimeout(() => setAlert(null), 5000);
  };

  const handleSubmit = async () => {
    try {
      if (editingType) {
        await api.put(`/entity-types/${editingType.entity_type_id}`, formData);
        showAlert('success', 'Entity type updated successfully');
      } else {
        await api.post('/entity-types', formData);
        showAlert('success', 'Entity type created successfully');
      }
      setShowForm(false);
      setEditingType(null);
      resetForm();
      loadTypes();
    } catch (err) {
      showAlert('error', 'Failed to save entity type');
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Are you sure you want to delete this entity type?')) return;
    try {
      await api.delete(`/entity-types/${id}`);
      showAlert('success', 'Entity type deleted successfully');
      loadTypes();
    } catch (err) {
      showAlert('error', 'Failed to delete entity type');
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
      is_system: type.is_system || false,
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
      is_system: false,
      sort_order: 0
    });
  };

  if (loading) return <div className="p-8 text-center">Loading...</div>;

  return (
    <div className="p-6">
      {alert && <Alert type={alert.type} message={alert.message} onClose={() => setAlert(null)} />}

      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold flex items-center gap-2">
          <Database className="w-8 h-8" />
          Entity Types ({types.length})
        </h1>
        <button
          onClick={() => { setShowForm(true); setEditingType(null); resetForm(); }}
          className="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700 transition-colors"
        >
          <Plus className="w-4 h-4" /> New Type
        </button>
      </div>

      <Modal
        isOpen={showForm}
        onClose={() => { setShowForm(false); setEditingType(null); }}
        title={editingType ? 'Edit Entity Type' : 'New Entity Type'}
      >
        <div className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium mb-1">Type Code *</label>
              <input
                type="text"
                value={formData.type_code}
                onChange={(e) => setFormData({...formData, type_code: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="hospital, zone, department"
              />
              <p className="text-xs text-gray-500 mt-1">Only lowercase, numbers and underscores</p>
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Code Prefix</label>
              <input
                type="text"
                value={formData.code_prefix}
                onChange={(e) => setFormData({...formData, code_prefix: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="HS, ZN, DP"
              />
              <p className="text-xs text-gray-500 mt-1">Prefix for entity codes</p>
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Type Name (Vietnamese) *</label>
              <input
                type="text"
                value={formData.type_name}
                onChange={(e) => setFormData({...formData, type_name: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="Bệnh viện, Khu vực"
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Type Name (English)</label>
              <input
                type="text"
                value={formData.type_name_en}
                onChange={(e) => setFormData({...formData, type_name_en: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="Hospital, Zone"
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Icon (Emoji)</label>
              <input
                type="text"
                value={formData.icon}
                onChange={(e) => setFormData({...formData, icon: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-2xl"
                placeholder="🏥"
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Color</label>
              <div className="flex gap-2">
                <input
                  type="color"
                  value={formData.color}
                  onChange={(e) => setFormData({...formData, color: e.target.value})}
                  className="w-16 h-10 border rounded cursor-pointer"
                />
                <input
                  type="text"
                  value={formData.color}
                  onChange={(e) => setFormData({...formData, color: e.target.value})}
                  className="flex-1 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                />
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Sort Order</label>
              <input
                type="number"
                value={formData.sort_order}
                onChange={(e) => setFormData({...formData, sort_order: parseInt(e.target.value) || 0})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              />
            </div>
            <div className="flex flex-col gap-2">
              <label className="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={formData.is_active}
                  onChange={(e) => setFormData({...formData, is_active: e.target.checked})}
                  className="w-4 h-4"
                />
                <span className="text-sm font-medium">Active</span>
              </label>
              <label className="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={formData.is_system}
                  onChange={(e) => setFormData({...formData, is_system: e.target.checked})}
                  className="w-4 h-4"
                />
                <span className="text-sm font-medium">System Type</span>
              </label>
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium mb-1">Description</label>
            <textarea
              value={formData.description}
              onChange={(e) => setFormData({...formData, description: e.target.value})}
              className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              rows={3}
              placeholder="Description for this entity type..."
            />
          </div>
          <div className="flex gap-2 pt-4">
            <button
              onClick={handleSubmit}
              className="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 flex items-center gap-2 transition-colors"
            >
              <Save className="w-4 h-4" /> Save
            </button>
            <button
              onClick={() => { setShowForm(false); setEditingType(null); }}
              className="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400 transition-colors"
            >
              Cancel
            </button>
          </div>
        </div>
      </Modal>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prefix</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">System</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {types.map(type => (
              <tr key={type.entity_type_id} className="hover:bg-gray-50">
                <td className="px-6 py-4 text-2xl">{type.icon || '📦'}</td>
                <td className="px-6 py-4">
                  <span className="px-2 py-1 rounded text-sm font-mono" style={{backgroundColor: type.color + '20', color: type.color}}>
                    {type.type_code}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <div className="font-medium">{type.type_name}</div>
                  {type.type_name_en && <div className="text-sm text-gray-500">{type.type_name_en}</div>}
                </td>
                <td className="px-6 py-4">
                  {type.code_prefix ? (
                    <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-mono">
                      {type.code_prefix}
                    </span>
                  ) : <span className="text-gray-400">-</span>}
                </td>
                <td className="px-6 py-4">
                  <span className={`px-2 py-1 rounded text-sm ${type.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                    {type.is_active ? 'Active' : 'Inactive'}
                  </span>
                </td>
                <td className="px-6 py-4">
                  {type.is_system && <span className="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm">System</span>}
                </td>
                <td className="px-6 py-4">
                  <div className="flex gap-2">
                    <button
                      onClick={() => handleEdit(type)}
                      className="text-blue-600 hover:text-blue-800"
                      title="Edit"
                    >
                      <Edit2 className="w-4 h-4" />
                    </button>
                    {!type.is_system && (
                      <button
                        onClick={() => handleDelete(type.entity_type_id)}
                        className="text-red-600 hover:text-red-800"
                        title="Delete"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    )}
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
  const [editingAttr, setEditingAttr] = useState(null);
  const [alert, setAlert] = useState(null);
  const [formData, setFormData] = useState({
    entity_type_id: null,
    attribute_code: '',
    attribute_label: '',
    backend_type: 'varchar',
    frontend_input: 'text',
    is_required: false,
    is_unique: false,
    is_searchable: true,
    is_filterable: false,
    default_value: '',
    placeholder: '',
    help_text: '',
    sort_order: 0,
    validation_rules: {},
    max_file_count: 1,
    allowed_extensions: '',
    max_file_size_kb: 2048,
    options: []
  });

  const backendTypes = {
    varchar: 'VARCHAR (Text)',
    text: 'TEXT (Long Text)',
    int: 'INTEGER (Number)',
    decimal: 'DECIMAL (Decimal)',
    datetime: 'DATETIME (Date/Time)',
    file: 'FILE (File Upload)'
  };

  const frontendInputs = {
    varchar: ['text', 'select', 'yesno'],
    text: ['textarea', 'multiselect'],
    int: ['text', 'select', 'yesno'],
    decimal: ['text'],
    datetime: ['date', 'datetime'],
    file: ['file']
  };

  const inputLabels = {
    text: '📝 Text Input',
    textarea: '📄 Textarea',
    select: '📋 Select',
    multiselect: '☑️ Multiselect',
    date: '📅 Date',
    datetime: '🕐 DateTime',
    yesno: '✅ Yes/No',
    file: '📎 File'
  };

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const typesData = await api.get('/entity-types');
      setTypes(typesData);
      loadAttributes();
    } catch (err) {
      showAlert('error', 'Failed to load data');
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

  const showAlert = (type, message) => {
    setAlert({ type, message });
    setTimeout(() => setAlert(null), 5000);
  };

  const handleSubmit = async () => {
    try {
      if (editingAttr) {
        await api.put(`/attributes/${editingAttr.attribute_id}`, formData);
        showAlert('success', 'Attribute updated successfully');
      } else {
        const result = await api.post('/attributes', formData);
        if (result.success) {
          showAlert('success', 'Attribute created successfully');
        } else {
          showAlert('error', result.message || 'Failed to create attribute');
          return;
        }
      }
      setShowForm(false);
      setEditingAttr(null);
      resetForm();
      loadAttributes();
    } catch (err) {
      showAlert('error', 'Failed to save attribute');
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Are you sure you want to delete this attribute?')) return;
    try {
      await api.delete(`/attributes/${id}`);
      showAlert('success', 'Attribute deleted successfully');
      loadAttributes();
    } catch (err) {
      showAlert('error', 'Failed to delete attribute');
    }
  };

  const resetForm = () => {
    setFormData({
      entity_type_id: null,
      attribute_code: '',
      attribute_label: '',
      backend_type: 'varchar',
      frontend_input: 'text',
      is_required: false,
      is_unique: false,
      is_searchable: true,
      is_filterable: false,
      default_value: '',
      placeholder: '',
      help_text: '',
      sort_order: 0,
      validation_rules: {},
      max_file_count: 1,
      allowed_extensions: '',
      max_file_size_kb: 2048,
      options: []
    });
  };

  const addOption = () => {
    setFormData({
      ...formData,
      options: [...formData.options, { label: '', is_default: false }]
    });
  };

  const removeOption = (index) => {
    const newOptions = formData.options.filter((_, i) => i !== index);
    setFormData({ ...formData, options: newOptions });
  };

  const updateOption = (index, field, value) => {
    const newOptions = [...formData.options];
    newOptions[index][field] = value;
    setFormData({ ...formData, options: newOptions });
  };

  if (loading) return <div className="p-8 text-center">Loading...</div>;

  return (
    <div className="p-6">
      {alert && <Alert type={alert.type} message={alert.message} onClose={() => setAlert(null)} />}

      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold flex items-center gap-2">
          <Boxes className="w-8 h-8" />
          Attributes ({attributes.length})
        </h1>
        <button
          onClick={() => { setShowForm(true); setEditingAttr(null); resetForm(); }}
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
          className="border rounded px-3 py-2 w-64 focus:ring-2 focus:ring-blue-500 outline-none"
        >
          <option value="all">All / Shared Attributes</option>
          {types.map(type => (
            <option key={type.entity_type_id} value={type.entity_type_id}>
              {type.icon} {type.type_name}
            </option>
          ))}
        </select>
      </div>

      <Modal
        isOpen={showForm}
        onClose={() => { setShowForm(false); setEditingAttr(null); }}
        title={editingAttr ? 'Edit Attribute' : 'New Attribute'}
        size="max-w-4xl"
      >
        <div className="space-y-6">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium mb-1">Entity Type</label>
              <select
                value={formData.entity_type_id || ''}
                onChange={(e) => setFormData({...formData, entity_type_id: e.target.value ? parseInt(e.target.value) : null})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              >
                <option value="">Shared (All Types)</option>
                {types.map(type => (
                  <option key={type.entity_type_id} value={type.entity_type_id}>
                    {type.icon} {type.type_name}
                  </option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Sort Order</label>
              <input
                type="number"
                value={formData.sort_order}
                onChange={(e) => setFormData({...formData, sort_order: parseInt(e.target.value) || 0})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Attribute Code *</label>
              <input
                type="text"
                value={formData.attribute_code}
                onChange={(e) => setFormData({...formData, attribute_code: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none font-mono"
                placeholder="dia_chi, so_dien_thoai"
              />
              <p className="text-xs text-gray-500 mt-1">Lowercase, numbers, underscores only</p>
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Attribute Label *</label>
              <input
                type="text"
                value={formData.attribute_label}
                onChange={(e) => setFormData({...formData, attribute_label: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="Địa chỉ, Số điện thoại"
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Backend Type *</label>
              <select
                value={formData.backend_type}
                onChange={(e) => {
                  const newType = e.target.value;
                  const validInputs = frontendInputs[newType];
                  setFormData({
                    ...formData,
                    backend_type: newType,
                    frontend_input: validInputs[0]
                  });
                }}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              >
                {Object.entries(backendTypes).map(([key, label]) => (
                  <option key={key} value={key}>{label}</option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Frontend Input *</label>
              <select
                value={formData.frontend_input}
                onChange={(e) => setFormData({...formData, frontend_input: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              >
                {frontendInputs[formData.backend_type]?.map(input => (
                  <option key={input} value={input}>{inputLabels[input]}</option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Placeholder</label>
              <input
                type="text"
                value={formData.placeholder}
                onChange={(e) => setFormData({...formData, placeholder: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="Enter placeholder text..."
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Default Value</label>
              <input
                type="text"
                value={formData.default_value}
                onChange={(e) => setFormData({...formData, default_value: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">Help Text</label>
            <textarea
              value={formData.help_text}
              onChange={(e) => setFormData({...formData, help_text: e.target.value})}
              className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              rows={2}
              placeholder="Help text for users..."
            />
          </div>

          <div className="grid grid-cols-4 gap-4">
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                checked={formData.is_required}
                onChange={(e) => setFormData({...formData, is_required: e.target.checked})}
                className="w-4 h-4"
              />
              <span className="text-sm font-medium">Required</span>
            </label>
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                checked={formData.is_unique}
                onChange={(e) => setFormData({...formData, is_unique: e.target.checked})}
                className="w-4 h-4"
              />
              <span className="text-sm font-medium">Unique</span>
            </label>
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                checked={formData.is_searchable}
                onChange={(e) => setFormData({...formData, is_searchable: e.target.checked})}
                className="w-4 h-4"
              />
              <span className="text-sm font-medium">Searchable</span>
            </label>
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                checked={formData.is_filterable}
                onChange={(e) => setFormData({...formData, is_filterable: e.target.checked})}
                className="w-4 h-4"
              />
              <span className="text-sm font-medium">Filterable</span>
            </label>
          </div>

          {formData.backend_type === 'file' && (
            <div className="border rounded p-4 bg-gray-50">
              <h3 className="font-medium mb-3">File Upload Settings</h3>
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1">Max Files</label>
                  <input
                    type="number"
                    value={formData.max_file_count}
                    onChange={(e) => setFormData({...formData, max_file_count: parseInt(e.target.value) || 1})}
                    className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                    min="1"
                    max="10"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Allowed Extensions</label>
                  <input
                    type="text"
                    value={formData.allowed_extensions}
                    onChange={(e) => setFormData({...formData, allowed_extensions: e.target.value})}
                    className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="jpg,png,pdf"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Max Size (KB)</label>
                  <input
                    type="number"
                    value={formData.max_file_size_kb}
                    onChange={(e) => setFormData({...formData, max_file_size_kb: parseInt(e.target.value) || 2048})}
                    className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                  />
                </div>
              </div>
            </div>
          )}

          {(formData.frontend_input === 'select' || formData.frontend_input === 'multiselect') && (
            <div className="border rounded p-4 bg-gray-50">
              <div className="flex justify-between items-center mb-3">
                <h3 className="font-medium">Options</h3>
                <button
                  onClick={addOption}
                  className="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1"
                  type="button"
                >
                  <Plus className="w-4 h-4" /> Add Option
                </button>
              </div>
              <div className="space-y-2">
                {formData.options.map((option, index) => (
                  <div key={index} className="flex gap-2 items-center">
                    <input
                      type="text"
                      value={option.label}
                      onChange={(e) => updateOption(index, 'label', e.target.value)}
                      className="flex-1 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                      placeholder="Option label"
                    />
                    <label className="flex items-center gap-1">
                      <input
                        type="checkbox"
                        checked={option.is_default}
                        onChange={(e) => updateOption(index, 'is_default', e.target.checked)}
                        className="w-4 h-4"
                      />
                      <span className="text-sm">Default</span>
                    </label>
                    <button
                      onClick={() => removeOption(index)}
                      className="text-red-600 hover:text-red-800"
                      type="button"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                ))}
                {formData.options.length === 0 && (
                  <p className="text-sm text-gray-500 text-center py-2">No options added yet</p>
                )}
              </div>
            </div>
          )}

          <div className="flex gap-2 pt-4 border-t">
            <button
              onClick={handleSubmit}
              className="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 flex items-center gap-2"
              type="button"
            >
              <Save className="w-4 h-4" /> Save Attribute
            </button>
            <button
              onClick={() => { setShowForm(false); setEditingAttr(null); }}
              className="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400"
              type="button"
            >
              Cancel
            </button>
          </div>
        </div>
      </Modal>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Label</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Backend Type</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frontend Input</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Flags</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {attributes.map(attr => (
              <tr key={attr.attribute_id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-mono text-sm">{attr.attribute_code}</td>
                <td className="px-6 py-4">
                  <div className="font-medium">{attr.attribute_label}</div>
                  {attr.help_text && <div className="text-xs text-gray-500">{attr.help_text}</div>}
                </td>
                <td className="px-6 py-4">
                  <span className="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm">
                    {attr.backend_type}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                    {inputLabels[attr.frontend_input]}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <div className="flex gap-1">
                    {attr.is_required && <span className="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs">REQ</span>}
                    {attr.is_unique && <span className="px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded text-xs">UNQ</span>}
                    {attr.is_searchable && <span className="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs">SEA</span>}
                    {attr.is_filterable && <span className="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">FIL</span>}
                  </div>
                </td>
                <td className="px-6 py-4">
                  <div className="flex gap-2">
                    <button
                      className="text-blue-600 hover:text-blue-800"
                      title="Edit"
                    >
                      <Edit2 className="w-4 h-4" />
                    </button>
                    <button
                      onClick={() => handleDelete(attr.attribute_id)}
                      className="text-red-600 hover:text-red-800"
                      title="Delete"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        {attributes.length === 0 && (
          <div className="p-8 text-center text-gray-500">No attributes found</div>
        )}
      </div>
    </div>
  );
};

// Entities Management (CONTINUED)
const EntitiesView = () => {
  const [entities, setEntities] = useState([]);
  const [types, setTypes] = useState([]);
  const [attributes, setAttributes] = useState([]);
  const [selectedType, setSelectedType] = useState(null);
  const [loading, setLoading] = useState(true);
  const [expandedNodes, setExpandedNodes] = useState(new Set());
  const [showForm, setShowForm] = useState(false);
  const [editingEntity, setEditingEntity] = useState(null);
  const [alert, setAlert] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [formData, setFormData] = useState({
    entity_type_id: null,
    entity_code: '',
    entity_name: '',
    parent_id: null,
    description: '',
    is_active: true,
    sort_order: 0,
    attributes: {}
  });

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
      showAlert('error', 'Failed to load types');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (selectedType) {
      loadEntities();
      loadAttributes();
    }
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

  const loadAttributes = async () => {
    try {
      const data = await api.get(`/entity-types/${selectedType}/attributes`);
      setAttributes(data);
    } catch (err) {
      console.error(err);
    }
  };

  const showAlert = (type, message) => {
    setAlert({ type, message });
    setTimeout(() => setAlert(null), 5000);
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

  const handleSubmit = async () => {
    try {
      const payload = {
        entity_type_id: formData.entity_type_id || selectedType,
        entity_code: formData.entity_code,
        entity_name: formData.entity_name,
        parent_id: formData.parent_id,
        description: formData.description,
        is_active: formData.is_active,
        sort_order: formData.sort_order
      };

      // Add dynamic attributes
      Object.entries(formData.attributes).forEach(([attrId, value]) => {
        payload[`attr_${attrId}`] = value;
      });

      if (editingEntity) {
        await api.put(`/entities/${editingEntity.entity_id}`, payload);
        showAlert('success', 'Entity updated successfully');
      } else {
        await api.post('/entities', payload);
        showAlert('success', 'Entity created successfully');
      }
      
      setShowForm(false);
      setEditingEntity(null);
      resetForm();
      loadEntities();
    } catch (err) {
      showAlert('error', 'Failed to save entity');
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Are you sure you want to delete this entity?')) return;
    try {
      await api.delete(`/entities/${id}`);
      showAlert('success', 'Entity deleted successfully');
      loadEntities();
    } catch (err) {
      showAlert('error', 'Failed to delete entity');
    }
  };

  const handleView = async (id) => {
    try {
      const data = await api.get(`/entities/${id}`);
      console.log('Entity details:', data);
      showAlert('info', 'View functionality - check console');
    } catch (err) {
      showAlert('error', 'Failed to load entity details');
    }
  };

  const resetForm = () => {
    setFormData({
      entity_type_id: null,
      entity_code: '',
      entity_name: '',
      parent_id: null,
      description: '',
      is_active: true,
      sort_order: 0,
      attributes: {}
    });
  };

  const setAttributeValue = (attrId, value) => {
    setFormData({
      ...formData,
      attributes: {
        ...formData.attributes,
        [attrId]: value
      }
    });
  };

  const renderAttributeInput = (attr) => {
    const value = formData.attributes[attr.attribute_id] || '';
    
    switch (attr.frontend_input) {
      case 'textarea':
        return (
          <textarea
            value={value}
            onChange={(e) => setAttributeValue(attr.attribute_id, e.target.value)}
            className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
            rows={3}
            placeholder={attr.placeholder}
          />
        );
      case 'select':
        return (
          <select
            value={value}
            onChange={(e) => setAttributeValue(attr.attribute_id, e.target.value)}
            className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          >
            <option value="">-- Select --</option>
            {/* Options would be loaded from attr.options */}
          </select>
        );
      case 'yesno':
        return (
          <label className="flex items-center gap-2">
            <input
              type="checkbox"
              checked={!!value}
              onChange={(e) => setAttributeValue(attr.attribute_id, e.target.checked)}
              className="w-4 h-4"
            />
            <span className="text-sm">Yes</span>
          </label>
        );
      case 'date':
        return (
          <input
            type="date"
            value={value}
            onChange={(e) => setAttributeValue(attr.attribute_id, e.target.value)}
            className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          />
        );
      case 'datetime':
        return (
          <input
            type="datetime-local"
            value={value}
            onChange={(e) => setAttributeValue(attr.attribute_id, e.target.value)}
            className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          />
        );
      default:
        return (
          <input
            type="text"
            value={value}
            onChange={(e) => setAttributeValue(attr.attribute_id, e.target.value)}
            className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
            placeholder={attr.placeholder}
          />
        );
    }
  };

  const filteredEntities = entities.filter(e => 
    e.entity_code.toLowerCase().includes(searchQuery.toLowerCase()) ||
    e.entity_name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const renderTree = (items, parentId = null, level = 0) => {
    return items
      .filter(item => item.parent_id === parentId)
      .map(item => {
        const hasChildren = items.some(i => i.parent_id === item.entity_id);
        const isExpanded = expandedNodes.has(item.entity_id);

        return (
          <div key={item.entity_id}>
            <div
              className="flex items-center gap-2 py-2 px-4 hover:bg-gray-50 group"
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
              <span className="font-medium flex-1">{item.entity_name}</span>
              <span className="text-xs text-gray-500">Lv.{item.level}</span>
              <div className="opacity-0 group-hover:opacity-100 flex gap-1 transition-opacity">
                <button
                  onClick={() => handleView(item.entity_id)}
                  className="text-blue-600 hover:text-blue-800 p-1"
                  title="View"
                >
                  <Eye className="w-4 h-4" />
                </button>
                <button
                  className="text-green-600 hover:text-green-800 p-1"
                  title="Edit"
                >
                  <Edit2 className="w-4 h-4" />
                </button>
                <button
                  onClick={() => handleDelete(item.entity_id)}
                  className="text-red-600 hover:text-red-800 p-1"
                  title="Delete"
                >
                  <Trash2 className="w-4 h-4" />
                </button>
              </div>
            </div>
            {isExpanded && hasChildren && renderTree(items, item.entity_id, level + 1)}
          </div>
        );
      });
  };

  if (loading) return <div className="p-8 text-center">Loading...</div>;

  return (
    <div className="p-6">
      {alert && <Alert type={alert.type} message={alert.message} onClose={() => setAlert(null)} />}

      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold flex items-center gap-2">
          <FileText className="w-8 h-8" />
          Entities ({filteredEntities.length})
        </h1>
        <button
          onClick={() => { setShowForm(true); setEditingEntity(null); resetForm(); }}
          className="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700"
        >
          <Plus className="w-4 h-4" /> New Entity
        </button>
      </div>

      <div className="flex gap-4 mb-4">
        <div className="flex-1">
          <label className="block text-sm font-medium mb-2">Entity Type</label>
          <select
            value={selectedType || ''}
            onChange={(e) => setSelectedType(parseInt(e.target.value))}
            className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          >
            {types.map(type => (
              <option key={type.entity_type_id} value={type.entity_type_id}>
                {type.icon} {type.type_name}
              </option>
            ))}
          </select>
        </div>
        <div className="flex-1">
          <label className="block text-sm font-medium mb-2">Search</label>
          <div className="relative">
            <Search className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full border rounded px-10 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              placeholder="Search entities..."
            />
          </div>
        </div>
      </div>

      <Modal
        isOpen={showForm}
        onClose={() => { setShowForm(false); setEditingEntity(null); }}
        title={editingEntity ? 'Edit Entity' : 'New Entity'}
        size="max-w-4xl"
      >
        <div className="space-y-6">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium mb-1">Entity Code *</label>
              <input
                type="text"
                value={formData.entity_code}
                onChange={(e) => setFormData({...formData, entity_code: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none font-mono"
                placeholder="HS-001, ZN-COOK-01"
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Entity Name *</label>
              <input
                type="text"
                value={formData.entity_name}
                onChange={(e) => setFormData({...formData, entity_name: e.target.value})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="Hospital Name, Zone Name"
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Parent Entity</label>
              <select
                value={formData.parent_id || ''}
                onChange={(e) => setFormData({...formData, parent_id: e.target.value ? parseInt(e.target.value) : null})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              >
                <option value="">-- Root (No Parent) --</option>
                {entities.map(e => (
                  <option key={e.entity_id} value={e.entity_id}>
                    {'─'.repeat(e.level)} {e.entity_name}
                  </option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Sort Order</label>
              <input
                type="number"
                value={formData.sort_order}
                onChange={(e) => setFormData({...formData, sort_order: parseInt(e.target.value) || 0})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">Description</label>
            <textarea
              value={formData.description}
              onChange={(e) => setFormData({...formData, description: e.target.value})}
              className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              rows={3}
            />
          </div>

          <div>
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                checked={formData.is_active}
                onChange={(e) => setFormData({...formData, is_active: e.target.checked})}
                className="w-4 h-4"
              />
              <span className="text-sm font-medium">Active</span>
            </label>
          </div>

          {attributes.length > 0 && (
            <div className="border-t pt-6">
              <h3 className="font-medium mb-4 flex items-center gap-2">
                <Settings className="w-5 h-5" />
                Dynamic Attributes
              </h3>
              <div className="grid grid-cols-2 gap-4">
                {attributes.map(attr => (
                  <div key={attr.attribute_id}>
                    <label className="block text-sm font-medium mb-1">
                      {attr.attribute_label}
                      {attr.is_required && <span className="text-red-600 ml-1">*</span>}
                    </label>
                    {renderAttributeInput(attr)}
                    {attr.help_text && (
                      <p className="text-xs text-gray-500 mt-1">{attr.help_text}</p>
                    )}
                  </div>
                ))}
              </div>
            </div>
          )}

          <div className="flex gap-2 pt-4 border-t">
            <button
              onClick={handleSubmit}
              className="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 flex items-center gap-2"
              type="button"
            >
              <Save className="w-4 h-4" /> Save Entity
            </button>
            <button
              onClick={() => { setShowForm(false); setEditingEntity(null); }}
              className="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400"
              type="button"
            >
              Cancel
            </button>
          </div>
        </div>
      </Modal>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        {filteredEntities.length === 0 ? (
          <div className="p-8 text-center text-gray-500">No entities found</div>
        ) : (
          <div className="divide-y divide-gray-200">
            {renderTree(filteredEntities)}
          </div>
        )}
      </div>
    </div>
  );
};

// Relations Management (FULL)
const RelationsView = () => {
  const [relations, setRelations] = useState([]);
  const [entities, setEntities] = useState([]);
  const [types, setTypes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [alert, setAlert] = useState(null);
  const [selectedEntity, setSelectedEntity] = useState(null);
  const [formData, setFormData] = useState({
    source_entity_id: null,
    target_entity_id: null,
    relation_type: '',
    is_active: true,
    sort_order: 0
  });

  const relationTypes = [
    'belongs_to',
    'has_many',
    'related_to',
    'depends_on',
    'parent_of',
    'child_of'
  ];

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [typesData] = await Promise.all([
        api.get('/entity-types')
      ]);
      setTypes(typesData);
    } catch (err) {
      showAlert('error', 'Failed to load data');
    } finally {
      setLoading(false);
    }
  };

  const loadRelations = async (entityId) => {
    try {
      const data = await api.get(`/entities/${entityId}/relations`);
      if (data.success) {
        setRelations([...data.data.outgoing_relations, ...data.data.incoming_relations]);
      }
    } catch (err) {
      console.error(err);
    }
  };

  const showAlert = (type, message) => {
    setAlert({ type, message });
    setTimeout(() => setAlert(null), 5000);
  };

  const handleSubmit = async () => {
    try {
      await api.post('/relations', formData);
      showAlert('success', 'Relation created successfully');
      setShowForm(false);
      resetForm();
      if (selectedEntity) loadRelations(selectedEntity);
    } catch (err) {
      showAlert('error', 'Failed to create relation');
    }
  };

  const handleDelete = async (id) => {
    if (!confirm('Are you sure?')) return;
    try {
      await api.delete(`/relations/${id}`);
      showAlert('success', 'Relation deleted');
      if (selectedEntity) loadRelations(selectedEntity);
    } catch (err) {
      showAlert('error', 'Failed to delete relation');
    }
  };

  const resetForm = () => {
    setFormData({
      source_entity_id: null,
      target_entity_id: null,
      relation_type: '',
      is_active: true,
      sort_order: 0
    });
  };

  if (loading) return <div className="p-8 text-center">Loading...</div>;

  return (
    <div className="p-6">
      {alert && <Alert type={alert.type} message={alert.message} onClose={() => setAlert(null)} />}

      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold flex items-center gap-2">
          <Link className="w-8 h-8" />
          Entity Relations ({relations.length})
        </h1>
        <button
          onClick={() => { setShowForm(true); resetForm(); }}
          className="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700"
        >
          <Plus className="w-4 h-4" /> New Relation
        </button>
      </div>

      <div className="mb-4">
        <label className="block text-sm font-medium mb-2">Select Entity to View Relations</label>
        <div className="flex gap-2">
          <select
            value={selectedEntity || ''}
            onChange={(e) => {
              const id = parseInt(e.target.value);
              setSelectedEntity(id);
              loadRelations(id);
            }}
            className="flex-1 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          >
            <option value="">-- Select Entity --</option>
            {entities.map(e => (
              <option key={e.entity_id} value={e.entity_id}>
                {e.entity_code} - {e.entity_name}
              </option>
            ))}
          </select>
        </div>
      </div>

      <Modal
        isOpen={showForm}
        onClose={() => setShowForm(false)}
        title="New Relation"
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-1">Source Entity *</label>
            <select
              value={formData.source_entity_id || ''}
              onChange={(e) => setFormData({...formData, source_entity_id: parseInt(e.target.value)})}
              className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
            >
              <option value="">-- Select --</option>
              {entities.map(e => (
                <option key={e.entity_id} value={e.entity_id}>
                  {e.entity_code} - {e.entity_name}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">Relation Type *</label>
            <select
              value={formData.relation_type}
              onChange={(e) => setFormData({...formData, relation_type: e.target.value})}
              className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
            >
              <option value="">-- Select --</option>
              {relationTypes.map(type => (
                <option key={type} value={type}>{type}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">Target Entity *</label>
            <select
              value={formData.target_entity_id || ''}
              onChange={(e) => setFormData({...formData, target_entity_id: parseInt(e.target.value)})}
              className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
            >
              <option value="">-- Select --</option>
              {entities.filter(e => e.entity_id !== formData.source_entity_id).map(e => (
                <option key={e.entity_id} value={e.entity_id}>
                  {e.entity_code} - {e.entity_name}
                </option>
              ))}
            </select>
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium mb-1">Sort Order</label>
              <input
                type="number"
                value={formData.sort_order}
                onChange={(e) => setFormData({...formData, sort_order: parseInt(e.target.value) || 0})}
                className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
              />
            </div>
            <div className="flex items-end">
              <label className="flex items-center gap-2 cursor-pointer">
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

          <div className="flex gap-2 pt-4 border-t">
            <button
              onClick={handleSubmit}
              className="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 flex items-center gap-2"
              type="button"
            >
              <Save className="w-4 h-4" /> Save Relation
            </button>
            <button
              onClick={() => setShowForm(false)}
              className="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400"
              type="button"
            >
              Cancel
            </button>
          </div>
        </div>
      </Modal>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        {!selectedEntity ? (
          <div className="p-8 text-center text-gray-500">
            <Link className="w-12 h-12 mx-auto mb-3 text-gray-400" />
            <p>Select an entity above to view its relations</p>
          </div>
        ) : relations.length === 0 ? (
          <div className="p-8 text-center text-gray-500">No relations found</div>
        ) : (
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Target</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {relations.map(rel => (
                <tr key={rel.relation_id} className="hover:bg-gray-50">
                  <td className="px-6 py-4">
                    <div className="font-medium">{rel.sourceEntity?.entity_name}</div>
                    <div className="text-sm text-gray-500">{rel.sourceEntity?.entity_code}</div>
                  </td>
                  <td className="px-6 py-4">
                    <span className="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm">
                      {rel.relation_type}
                    </span>
                  </td>
                  <td className="px-6 py-4">
                    <div className="font-medium">{rel.targetEntity?.entity_name}</div>
                    <div className="text-sm text-gray-500">{rel.targetEntity?.entity_code}</div>
                  </td>
                  <td className="px-6 py-4">
                    <span className={`px-2 py-1 rounded text-sm ${rel.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                      {rel.is_active ? 'Active' : 'Inactive'}
                    </span>
                  </td>
                  <td className="px-6 py-4">
                    <div className="flex gap-2">
                      <button
                        className="text-blue-600 hover:text-blue-800"
                        title="Edit"
                      >
                        <Edit2 className="w-4 h-4" />
                      </button>
                      <button
                        onClick={() => handleDelete(rel.relation_id)}
                        className="text-red-600 hover:text-red-800"
                        title="Delete"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
};

// Search View
const SearchView = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [searchResults, setSearchResults] = useState([]);
  const [loading, setLoading] = useState(false);
  const [alert, setAlert] = useState(null);

  const handleSearch = async () => {
    if (!searchQuery.trim()) {
      showAlert('error', 'Please enter search query');
      return;
    }

    setLoading(true);
    try {
      const data = await api.get(`/entities/search?q=${encodeURIComponent(searchQuery)}`);
      if (data.success) {
        setSearchResults(data.data);
        showAlert('success', `Found ${data.data.length} results`);
      }
    } catch (err) {
      showAlert('error', 'Search failed');
    } finally {
      setLoading(false);
    }
  };

  const showAlert = (type, message) => {
    setAlert({ type, message });
    setTimeout(() => setAlert(null), 5000);
  };

  return (
    <div className="p-6">
      {alert && <Alert type={alert.type} message={alert.message} onClose={() => setAlert(null)} />}

      <div className="mb-6">
        <h1 className="text-3xl font-bold flex items-center gap-2 mb-4">
          <Search className="w-8 h-8" />
          Search Entities
        </h1>
        <div className="flex gap-2">
          <div className="relative flex-1">
            <Search className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
              className="w-full border rounded-lg px-10 py-3 focus:ring-2 focus:ring-blue-500 outline-none"
              placeholder="Search by code, name, or description..."
            />
          </div>
          <button
            onClick={handleSearch}
            disabled={loading}
            className="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 transition-colors"
          >
            {loading ? 'Searching...' : 'Search'}
          </button>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        {searchResults.length === 0 ? (
          <div className="p-8 text-center text-gray-500">
            <Search className="w-12 h-12 mx-auto mb-3 text-gray-400" />
            <p>Enter a search query to find entities</p>
          </div>
        ) : (
          <div className="divide-y divide-gray-200">
            {searchResults.map(entity => (
              <div key={entity.entity_id} className="p-4 hover:bg-gray-50">
                <div className="flex items-start justify-between">
                  <div>
                    <div className="flex items-center gap-2 mb-1">
                      <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-mono">
                        {entity.entity_code}
                      </span>
                      <h3 className="font-medium text-lg">{entity.entity_name}</h3>
                    </div>
                    {entity.description && (
                      <p className="text-sm text-gray-600 mt-1">{entity.description}</p>
                    )}
                    <div className="flex gap-2 mt-2">
                      <span className="text-xs text-gray-500">Level: {entity.level}</span>
                      <span className="text-xs text-gray-500">Path: {entity.path}</span>
                    </div>
                  </div>
                  <button className="text-blue-600 hover:text-blue-800">
                    <Eye className="w-5 h-5" />
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
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
    { id: 'relations', label: 'Relations', icon: Link, component: RelationsView },
    { id: 'search', label: 'Search', icon: Search, component: SearchView }
  ];

  const CurrentComponent = navigation.find(n => n.id === currentView)?.component || EntityTypesView;

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white shadow-sm border-b sticky top-0 z-40">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex items-center justify-between h-16">
            <div className="flex items-center gap-2">
              <Database className="w-8 h-8 text-blue-600" />
              <div>
                <div className="text-xl font-bold">EAV Management System</div>
                <div className="text-xs text-gray-500">Entity-Attribute-Value Architecture</div>
              </div>
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

      <footer className="bg-white border-t mt-12">
        <div className="max-w-7xl mx-auto px-4 py-6">
          <div className="flex items-center justify-between text-sm text-gray-500">
            <div>© 2025 EAV Management System - Built with React</div>
            <div className="flex gap-4">
              <span className="flex items-center gap-1">
                <Database className="w-4 h-4" /> Laravel Backend API
              </span>
              <span className="flex items-center gap-1">
                <Boxes className="w-4 h-4" /> Dynamic Attributes
              </span>
              <span className="flex items-center gap-1">
                <FileText className="w-4 h-4" /> Hierarchical Entities
              </span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}