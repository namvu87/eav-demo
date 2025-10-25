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
                onChange={(e) => setFormData({...formData, is_required: e.