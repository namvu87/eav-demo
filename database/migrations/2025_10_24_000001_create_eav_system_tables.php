<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ==========================================
        // LAYER 1: ENTITY TYPES
        // ==========================================
        Schema::create('entity_types', function (Blueprint $table) {
            $table->id('entity_type_id');
            
            // Basic info
            $table->string('type_code', 100)->unique()->comment('plant, zone, hospital, department, product...');
            $table->string('type_name', 255)->comment('Nhà máy, Khu vực, Bệnh viện...');
            $table->string('type_name_en', 255)->nullable()->comment('Plant, Zone, Hospital...');
            
            // Display config
            $table->string('icon', 100)->nullable()->comment('Icon class hoặc emoji');
            $table->string('color', 20)->nullable()->comment('Màu hiển thị');
            $table->string('code_prefix', 10)->nullable()->comment('PL, ZN, HS, DP...');
            
            // Metadata
            $table->text('description')->nullable();
            $table->json('config')->nullable()->comment('Các cấu hình tùy chỉnh');
            
            // System
            $table->boolean('is_system')->default(0)->comment('Type do hệ thống tạo');
            $table->boolean('is_active')->default(1);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            $table->index('type_code');
            $table->index('is_active');
        });

        // ==========================================
        // LAYER 2: ATTRIBUTES
        // ==========================================
        Schema::create('attributes', function (Blueprint $table) {
            $table->id('attribute_id');
            
            // Thuộc về entity type nào (hoặc share across types)
            $table->unsignedBigInteger('entity_type_id')->nullable()->comment('NULL = shared attribute, INT = specific');
            
            // Basic info
            $table->string('attribute_code', 100)->comment('name, code, area_m2, manager...');
            $table->string('attribute_label', 255)->comment('Label hiển thị');
            
            // Data type & storage
            $table->enum('backend_type', ['varchar', 'text', 'int', 'decimal', 'datetime', 'file']);
            $table->enum('frontend_input', ['text', 'textarea', 'select', 'multiselect', 'date', 'datetime', 'yesno', 'file']);
            
            // Validation
            $table->boolean('is_required')->default(0);
            $table->boolean('is_unique')->default(0);
            $table->boolean('is_searchable')->default(1);
            $table->boolean('is_filterable')->default(0);
            
            $table->text('default_value')->nullable();
            $table->json('validation_rules')->nullable()->comment('email, url, min, max, regex...');
            
            // File upload config
            $table->integer('max_file_count')->default(1);
            $table->string('allowed_extensions', 255)->nullable()->comment('jpg,png,pdf,dwg');
            $table->integer('max_file_size_kb')->nullable();
            
            // UI config
            $table->string('placeholder', 255)->nullable();
            $table->text('help_text')->nullable();
            $table->string('frontend_class', 100)->nullable();
            $table->integer('sort_order')->default(0);
            
            // Group
            $table->unsignedBigInteger('group_id')->nullable();
            
            // System
            $table->boolean('is_system')->default(0);
            $table->boolean('is_user_defined')->default(1);
            
            $table->timestamps();
            
            $table->unique(['entity_type_id', 'attribute_code']);
            $table->index('backend_type');
            $table->index('is_searchable');
            
            $table->foreign('entity_type_id')->references('entity_type_id')->on('entity_types')->onDelete('cascade');
        });

        // ==========================================
        // LAYER 2.1: ATTRIBUTE GROUPS
        // ==========================================
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->id('group_id');
            $table->unsignedBigInteger('entity_type_id');
            
            $table->string('group_code', 100)->comment('general, technical, advanced');
            $table->string('group_name', 255)->comment('Thông tin cơ bản, Kỹ thuật...');
            
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(1);
            
            $table->timestamps();
            
            $table->unique(['entity_type_id', 'group_code']);
            $table->index(['entity_type_id', 'sort_order']);
            
            $table->foreign('entity_type_id')->references('entity_type_id')->on('entity_types')->onDelete('cascade');
        });

        // Add group_id foreign key to attributes
        Schema::table('attributes', function (Blueprint $table) {
            $table->foreign('group_id')->references('group_id')->on('attribute_groups')->onDelete('set null');
        });

        // ==========================================
        // LAYER 2.2: ATTRIBUTE OPTIONS
        // ==========================================
        Schema::create('attribute_options', function (Blueprint $table) {
            $table->id('option_id');
            $table->unsignedBigInteger('attribute_id');
            
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(0);
            
            $table->foreign('attribute_id')->references('attribute_id')->on('attributes')->onDelete('cascade');
            $table->index(['attribute_id', 'sort_order']);
        });

        Schema::create('attribute_options_value', function (Blueprint $table) {
            $table->id('value_id');
            $table->unsignedBigInteger('option_id');
            
            $table->string('value', 255)->comment('Label của option');
            
            $table->foreign('option_id')->references('option_id')->on('attribute_options')->onDelete('cascade');
            $table->index('option_id');
        });

        // ==========================================
        // LAYER 3: ENTITIES
        // ==========================================
        Schema::create('entities', function (Blueprint $table) {
            $table->id('entity_id');
            
            // Thuộc type gì
            $table->unsignedBigInteger('entity_type_id');
            
            // Core fields
            $table->string('entity_code', 100)->unique()->comment('PL-001, ZN-COOK-01, HS-001');
            $table->string('entity_name', 255);
            
            // Hierarchy support (tree structure)
            $table->unsignedBigInteger('parent_id')->nullable()->comment('NULL = root entity');
            $table->string('path', 1000)->nullable()->comment('Materialized path: /1/5/12/');
            $table->integer('level')->default(0)->comment('Độ sâu: 0=root, 1=level1...');
            
            // Metadata
            $table->text('description')->nullable();
            $table->json('metadata')->nullable()->comment('Dữ liệu mở rộng tự do');
            
            // Status
            $table->boolean('is_active')->default(1);
            $table->integer('sort_order')->default(0);
            
            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            
            $table->index('entity_type_id');
            $table->index('parent_id');
            $table->index(['path'], null, null, ['length' => 255]);
            $table->index('entity_code');
            $table->index('is_active');
            
            $table->foreign('entity_type_id')->references('entity_type_id')->on('entity_types')->onDelete('cascade');
            $table->foreign('parent_id')->references('entity_id')->on('entities')->onDelete('cascade');
        });

        // ==========================================
        // LAYER 4: ENTITY VALUES (6 tables)
        // ==========================================
        
        // VARCHAR values
        Schema::create('entity_values_varchar', function (Blueprint $table) {
            $table->id('value_id');
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('attribute_id');
            $table->string('value', 255)->nullable();
            
            $table->timestamps();
            
            $table->unique(['entity_id', 'attribute_id']);
            $table->index('attribute_id');
            $table->index(['value'], null, null, ['length' => 50]);
            
            $table->foreign('entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
            $table->foreign('attribute_id')->references('attribute_id')->on('attributes')->onDelete('cascade');
        });

        // TEXT values
        Schema::create('entity_values_text', function (Blueprint $table) {
            $table->id('value_id');
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('attribute_id');
            $table->text('value')->nullable();
            
            $table->timestamps();
            
            $table->unique(['entity_id', 'attribute_id']);
            $table->index('attribute_id');
            
            $table->foreign('entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
            $table->foreign('attribute_id')->references('attribute_id')->on('attributes')->onDelete('cascade');
        });

        // INT values
        Schema::create('entity_values_int', function (Blueprint $table) {
            $table->id('value_id');
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('attribute_id');
            $table->integer('value')->nullable();
            
            $table->timestamps();
            
            $table->unique(['entity_id', 'attribute_id']);
            $table->index('attribute_id');
            $table->index('value');
            
            $table->foreign('entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
            $table->foreign('attribute_id')->references('attribute_id')->on('attributes')->onDelete('cascade');
        });

        // DECIMAL values
        Schema::create('entity_values_decimal', function (Blueprint $table) {
            $table->id('value_id');
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('attribute_id');
            $table->decimal('value', 20, 4)->nullable();
            
            $table->timestamps();
            
            $table->unique(['entity_id', 'attribute_id']);
            $table->index('attribute_id');
            $table->index('value');
            
            $table->foreign('entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
            $table->foreign('attribute_id')->references('attribute_id')->on('attributes')->onDelete('cascade');
        });

        // DATETIME values
        Schema::create('entity_values_datetime', function (Blueprint $table) {
            $table->id('value_id');
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('attribute_id');
            $table->dateTime('value')->nullable();
            
            $table->timestamps();
            
            $table->unique(['entity_id', 'attribute_id']);
            $table->index('attribute_id');
            $table->index('value');
            
            $table->foreign('entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
            $table->foreign('attribute_id')->references('attribute_id')->on('attributes')->onDelete('cascade');
        });

        // FILE values
        Schema::create('entity_values_file', function (Blueprint $table) {
            $table->id('value_id');
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('attribute_id');
            
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->integer('file_size')->comment('bytes');
            $table->string('mime_type', 100);
            
            $table->timestamp('uploaded_at')->useCurrent();
            
            $table->index(['entity_id', 'attribute_id']);
            $table->index('attribute_id');
            
            $table->foreign('entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
            $table->foreign('attribute_id')->references('attribute_id')->on('attributes')->onDelete('cascade');
        });

        // ==========================================
        // LAYER 5: ENTITY RELATIONS
        // ==========================================
        Schema::create('entity_relations', function (Blueprint $table) {
            $table->id('relation_id');
            
            // Source và target entities
            $table->unsignedBigInteger('source_entity_id');
            $table->unsignedBigInteger('target_entity_id');
            
            // Loại quan hệ (TỰ DO ĐỊNH NGHĨA!)
            $table->string('relation_type', 100)->comment('parent_child, uses, supplies, manages, located_in, depends_on...');
            
            // Metadata bổ sung
            $table->json('relation_data')->nullable()->comment('Dữ liệu mở rộng cho relation');
            
            // Thứ tự và trạng thái
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(1);
            
            $table->timestamps();
            
            $table->index(['source_entity_id', 'relation_type']);
            $table->index(['target_entity_id', 'relation_type']);
            $table->index('relation_type');
            
            $table->foreign('source_entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
            $table->foreign('target_entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_relations');
        Schema::dropIfExists('entity_values_file');
        Schema::dropIfExists('entity_values_datetime');
        Schema::dropIfExists('entity_values_decimal');
        Schema::dropIfExists('entity_values_int');
        Schema::dropIfExists('entity_values_text');
        Schema::dropIfExists('entity_values_varchar');
        Schema::dropIfExists('entities');
        Schema::dropIfExists('attribute_options_value');
        Schema::dropIfExists('attribute_options');
        Schema::dropIfExists('attribute_groups');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('entity_types');
    }
};
