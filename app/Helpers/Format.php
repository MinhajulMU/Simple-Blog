<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class Format
{
    /**
     * Create unique slug function
     *
     * @param [type] $title
     * @param [type] $model
     * @return string
     */
    public static function createUniqueSlug($title, $model): string
    {
        // Generate the initial slug from the title
        $slug = Str::slug($title);
        // Check if the slug exists in the database
        $originalSlug = $slug;
        $count = 1;
        // Loop until we find a unique slug
        while ($model::where('slug', $slug)->exists()) {
            // Append the number to the slug to make it unique
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        return $slug;
    }
}
