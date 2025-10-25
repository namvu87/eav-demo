import React, { useState, useEffect } from 'react';
import { AlertCircle, Plus, Edit2, Trash2, Search, ChevronRight, ChevronDown, Filter, ArrowUpDown, Eye, Copy, Move, FileText, Database, Boxes, Link, Save, X, Upload, Settings } from 'lucide-react';

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
