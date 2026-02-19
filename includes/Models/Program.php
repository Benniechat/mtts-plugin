<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Program extends Model {
    protected static $table_name = 'mtts_programs';
}
