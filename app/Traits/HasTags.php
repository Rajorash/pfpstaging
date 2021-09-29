<?php

namespace App\Traits;

use App\Models\Tag;

trait HasTags
{
    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function syncTags($tags)
    {
        if (!is_array($tags)) {
            $tags = $this->parseTagString($tags);
        }

        $tagsDB = [];
        foreach ($tags as $tag) {
            $tagsDB[] = Tag::firstOrCreate(
                ['name' => $tag], ['name' => $tag],
            );
        }

        $this->tags()->sync(collect($tagsDB)->pluck('id')->toArray());

        return $this;
    }

    public function parseTagString($string): array
    {
        $tags = [];

        foreach (explode(',', trim($string)) as $tag) {
            $tag = trim($tag);
            if ($tag) {
                $tags[] = $tag;
            }
        }

        return $tags;
    }

    public function tagsAsString($separator = ', '): string
    {
        return implode($separator, $this->tags->pluck('name')->toArray());
    }
}
