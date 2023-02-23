<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 */


namespace Aimeos\Cms\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


/**
 * File model
 */
class File extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Prunable;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_files';


    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'mime' => '',
        'name' => '',
        'path' => '',
        'previews' => '{}',
    ];

    /**
     * The automatic casts for the attributes.
     *
     * @var array
     */
    protected $casts = [
        'previews' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];


    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where( 'deleted_at', '<=', now()->subMonths( 3 ) );
    }


    /**
     * Prepare the model for pruning.
     */
    protected function pruning(): void
    {
        $store = Storage::disk( config( 'cms.disk', 'public' ) );

        foreach( $this->previews as $path ) {
            $store->delete( $path );
        }

        $store->delete( $this->path );
    }
}
