<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminDatabaseSchema extends Migration
{
    public function getConnection()
    {
        return $this->config('database.connection') ?: config('database.default');
    }

    public function config($key)
    {
        return config('admin.'.$key);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create admin_users table
        Schema::create($this->config('database.users_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 120)->unique();
            $table->string('password', 80);
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });

        // Create admin_roles table
        Schema::create($this->config('database.roles_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        // Create admin_permissions table
        Schema::create($this->config('database.permissions_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->string('http_method')->nullable();
            $table->text('http_path')->nullable();
            $table->integer('order')->default(0);
            $table->bigInteger('parent_id')->default(0);
            $table->timestamps();
        });

        // Create admin_menu table
        Schema::create($this->config('database.menu_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('title', 50);
            $table->string('icon', 50)->nullable();
            $table->string('uri', 50)->nullable();
            $table->string('extension', 50)->default('');
            $table->tinyInteger('show')->default(1);
            $table->timestamps();
        });

        // Create admin_role_user table (pivot table)
        Schema::create($this->config('database.role_users_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('user_id');
            $table->unique(['role_id', 'user_id']);
            $table->timestamps();
        });

        // Create admin_role_permission table (pivot table)
        Schema::create($this->config('database.role_permissions_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('permission_id');
            $table->unique(['role_id', 'permission_id']);
            $table->timestamps();
        });

        // Create admin_role_menu table (pivot table)
        Schema::create($this->config('database.role_menu_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('menu_id');
            $table->unique(['role_id', 'menu_id']);
            $table->timestamps();
        });

        // Create admin_permission_menu table (pivot table)
        Schema::create($this->config('database.permission_menu_table'), function (Blueprint $table) {
            $table->bigInteger('permission_id');
            $table->bigInteger('menu_id');
            $table->unique(['permission_id', 'menu_id']);
            $table->timestamps();
        });

        // Create admin_settings table
        Schema::create($this->config('database.settings_table') ?: 'admin_settings', function (Blueprint $table) {
            $table->string('slug', 100)->primary();
            $table->longText('value');
            $table->timestamps();
        });

        // Create admin_extensions table
        Schema::create($this->config('database.extensions_table') ?: 'admin_extensions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 100)->unique();
            $table->string('version', 20)->default('');
            $table->tinyInteger('is_enabled')->default(0);
            $table->text('options')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Create admin_extension_histories table
        Schema::create($this->config('database.extension_histories_table') ?: 'admin_extension_histories', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('name', 100);
            $table->tinyInteger('type')->default(1);
            $table->string('version', 20)->default(0);
            $table->text('detail')->nullable();
            $table->index('name');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop all tables in reverse order (respecting foreign key constraints)
        Schema::dropIfExists($this->config('database.extension_histories_table') ?: 'admin_extension_histories');
        Schema::dropIfExists($this->config('database.extensions_table') ?: 'admin_extensions');
        Schema::dropIfExists($this->config('database.settings_table') ?: 'admin_settings');
        Schema::dropIfExists($this->config('database.permission_menu_table'));
        Schema::dropIfExists($this->config('database.role_menu_table'));
        Schema::dropIfExists($this->config('database.role_permissions_table'));
        Schema::dropIfExists($this->config('database.role_users_table'));
        Schema::dropIfExists($this->config('database.menu_table'));
        Schema::dropIfExists($this->config('database.permissions_table'));
        Schema::dropIfExists($this->config('database.roles_table'));
        Schema::dropIfExists($this->config('database.users_table'));
    }
}
