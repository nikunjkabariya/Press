<?php

namespace vicgonvt\Press\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use vicgonvt\Press\Post;
use vicgonvt\Press\Press;
use vicgonvt\Press\PressFileParser;

class ProcessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'press:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update blog posts.';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (Press::configNotPublished()) {
            return $this->warn('Please publish the config file by running ' .
                '\'php artisan vendor:publish --tag=press-config\'');
        }

        try {
            $posts = Press::driver()->fetchPosts();

            foreach ($posts as $post) {
                Post::create([
                    'identifier' => $post['identifier'],
                    'slug' => str_slug($post['title']),
                    'title' => $post['title'],
                    'body' => $post['body'],
                    'extra' => $post['extra'] ?? [],
                ]);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}