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
