<?php

use Silber\Bouncer\Database\Models;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBouncerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abilities_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->text('icon')->nullable();
        });

        Schema::create('abilities_groups_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ability_group_id')->unsigned();
            $table->char('default', 1)->default('Y');
            $table->char('locale', 2);
            $table->string('title', 191)->nullable();
            $table->string('description', 500)->nullable();
            $table->unique(
                ['ability_group_id', 'locale'],
                'groups_translations_group_id_locale_unique'
            );
            $table->foreign('ability_group_id')
            ->references('id')->on('abilities_groups')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
        
        Schema::create(Models::table('abilities'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned()->nullable();
            $table->string('name');
            $table->bigInteger('entity_id')->unsigned()->nullable();
            $table->string('entity_type')->nullable();
            $table->boolean('only_owned')->default(false);
            $table->json('options')->nullable();
            $table->integer('scope')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('group_id')
            ->references('id')->on('abilities_groups')
            ->onDelete('set NULL');
        });

        Schema::create(Models::table('abilities') . '_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ability_id')->unsigned();
            $table->char('default', 1)->default('Y');
            $table->char('locale', 2);
            $table->string('title', 191)->nullable();
            $table->text('description', 2500)->nullable();
            $table->unique(
                ['ability_id', 'locale'],
                Models::table('abilities') . '_ability_id_locale_unique'
            );
            $table->foreign('ability_id')
            ->references('id')->on(Models::table('abilities'))
            ->onDelete('CASCADE');
        });

        Schema::create(Models::table('roles'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            // $table->string('title')->nullable();
            $table->integer('level')->unsigned()->nullable();
            $table->integer('scope')->nullable()->index();

            $table->char('suspendable', 1)->default('Y');
            $table->char('updatable', 1)->default('Y');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(
                ['name', 'scope'],
                'roles_name_unique'
            );
        });

        Schema::create(Models::table('roles') . '_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned();
            $table->char('default', 1)->default('Y');
            $table->char('locale', 2);
            $table->string('title', 191)->nullable();
            $table->text('description', 2500)->nullable();
            $table->unique(
                ['role_id', 'locale'],
                Models::table('roles') . '_role_id_locale_unique'
            );
            $table->foreign('role_id')
            ->references('id')->on(Models::table('roles'))
            ->onDelete('CASCADE');
        });

        Schema::create(Models::table('assigned_roles'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('role_id')->unsigned()->index();
            $table->integer('entity_id')->unsigned();
            $table->string('entity_type');
            $table->integer('restricted_to_id')->unsigned()->nullable();
            $table->string('restricted_to_type')->nullable();
            $table->integer('scope')->nullable()->index();

            $table->index(
                ['entity_id', 'entity_type', 'scope'],
                'assigned_roles_entity_index'
            );

            $table->foreign('role_id')
            ->references('id')->on(Models::table('roles'))
            ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create(Models::table('permissions'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ability_id')->unsigned()->index();
            $table->integer('entity_id')->unsigned()->nullable();
            $table->string('entity_type')->nullable();
            $table->boolean('forbidden')->default(false);
            $table->integer('scope')->nullable()->index();

            $table->index(
                ['entity_id', 'entity_type', 'scope'],
                'permissions_entity_index'
            );

            $table->foreign('ability_id')
            ->references('id')->on(Models::table('abilities'))
            ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Models::table('permissions'));
        Schema::dropIfExists(Models::table('assigned_roles'));
        Schema::dropIfExists(Models::table('roles') . '_translations');
        Schema::dropIfExists(Models::table('roles'));
        Schema::dropIfExists(Models::table('abilities') . '_translations');
        Schema::dropIfExists(Models::table('abilities'));
        Schema::dropIfExists('abilities_groups_translations');
        Schema::dropIfExists('abilities_groups');
    }
}
