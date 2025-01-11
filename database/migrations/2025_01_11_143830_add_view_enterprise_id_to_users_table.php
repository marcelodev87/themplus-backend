<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        DB::statement('
            CREATE TRIGGER update_view_enterprise_id_on_delete
            BEFORE DELETE ON enterprises
            FOR EACH ROW
            BEGIN
                UPDATE users
                SET view_enterprise_id = OLD.id
                WHERE view_enterprise_id = OLD.id;
            END;
        ');
    }

    public function down()
    {
        DB::statement('DROP TRIGGER IF EXISTS update_view_enterprise_id_on_delete');
    }
};
