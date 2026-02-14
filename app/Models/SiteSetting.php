<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiteSetting extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'meta_title',
        'meta_description',
        'favicon',
        'menu_logo',
        'logo',
        'meta_header',
        'site_color',
        'meta_footer',
        'site_info',
        'header_custom_code',
        'footer_custom_code',
        'office_mobile',
        'office_email',
        'office_address',
        'banner',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'site_settings';
}
