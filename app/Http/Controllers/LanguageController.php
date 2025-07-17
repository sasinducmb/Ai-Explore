<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Str;



class LanguageController extends Controller
{
    public function switch($locale)
    {
        if (!in_array($locale, ['en', 'si', 'ta'])) {
            abort(400);
        }

        Cookie::queue('locale', $locale, 60 * 24 * 30); // 30 days
        App::setLocale($locale);

        return redirect()->back();
    }

    public function show($id)
    {
        $aiTools = $this->getAiTools();

        $tool = collect($aiTools)->firstWhere('id', (int) $id);

        if (!$tool) {
            abort(404);
        }

        return view('learn.show', compact('tool'));
    }




    private function getAiTools()
    {
        return [
            [
                'id' => 1,
                'img' => asset('asset/img/learn/1.png'),
                'icon' => '✍️',
                'title' => 'Quick, Draw!',
                'subtitle' => 'A fun game where AI guesses your doodles in real-time',
                'description' => 'Dive into an engaging and entertaining interactive drawing game where an advanced artificial intelligence system watches your every stroke and attempts to identify the objects or scenes you are sketching in real-time.',
                'youtubeUrl' => 'https://youtu.be/X8v1GWzZYJ4?si=ywiWbOOhpR3GeZDz',
            ],
            [
                'id' => 2,
                'img' => asset('asset/img/learn/2.png'),
                'icon' => '🌊',
                'title' => 'DeepSeek',
                'subtitle' => 'An AI tool that finds answers from large knowledge sources',
                'description' => 'Harness the power of an advanced AI-driven search tool designed to scour vast knowledge bases and deliver precise, comprehensive answers to your queries.',
                'youtubeUrl' => 'https://youtu.be/_8tcA9-14JQ?si=nJpoj1SvwLcd_6-d',
            ],
            [
                'id' => 3,
                'img' => asset('asset/img/learn/3.png'),
                'icon' => '📖',
                'title' => 'Storybird AI',
                'subtitle' => 'Helps you turn ideas into creative illustrated stories using AI',
                'description' => 'Unleash your storytelling potential with Storybird AI, a platform that transforms your imaginative ideas into beautifully illustrated stories using AI.',
                'youtubeUrl' => 'https://youtu.be/FpE6z2AOK-I?si=A1FsOkmV6O7AOd4Y',
            ],
            [
                'id' => 4,
                'img' => asset('asset/img/learn/4.png'),
                'icon' => '💬',
                'title' => 'ChatGPT',
                'subtitle' => 'A smart chat that understands and responds to your messages',
                'description' => 'Experience the future of conversation with ChatGPT, an intelligent chatbot powered by advanced AI that comprehends and responds to your messages.',
                'youtubeUrl' => 'https://youtu.be/Gaf_jCnA6mc?si=KJ7YIoEdmSyR1OT-',
            ],
            [
                'id' => 5,
                'img' => asset('asset/img/learn/5.png'),
                'icon' => '🎶',
                'title' => 'AI Duet',
                'subtitle' => 'Play a melody and an AI responds with its own musical notes',
                'description' => 'Embark on a musical journey with AI Duet where you play a melody and a sophisticated AI responds in real-time with its own notes.',
                'youtubeUrl' => 'https://www.youtube.com/watch?v=your_aiduet_video',
            ],
            [
                'id' => 6,
                'img' => asset('asset/img/learn/6.png'),
                'icon' => '🎨',
                'title' => 'Scribble Diffusion',
                'subtitle' => 'Transforms your rough sketches into refined images using AI',
                'description' => 'Elevate your artistic skills with Scribble Diffusion, an AI tool that transforms your sketches into polished, refined images.',
                'youtubeUrl' => 'https://www.youtube.com/watch?v=your_scribblediffusion_video',
            ],
            [
                'id' => 7,
                'img' => asset('asset/img/learn/7.png'),
                'icon' => '📊',
                'title' => 'Tome AI',
                'subtitle' => 'Creates structured presentation slides instantly from your input',
                'description' => 'Revolutionize presentation creation with Tome AI, which transforms your ideas into structured and visually appealing slides.',
                'youtubeUrl' => 'https://www.youtube.com/watch?v=your_tomeai_video',
            ],
            [
                'id' => 8,
                'img' => asset('asset/img/learn/8.png'),
                'icon' => '✏️',
                'title' => 'AutoDraw',
                'subtitle' => 'Suggests polished drawings based on your quick sketches',
                'description' => 'AutoDraw analyzes your quick sketches and offers polished versions in real-time.',
                'youtubeUrl' => 'https://www.youtube.com/watch?v=your_autodraw_video',
            ],
            [
                'id' => 9,
                'img' => asset('asset/img/learn/8.png'),
                'icon' => '🖌️',
                'title' => 'Figma',
                'subtitle' => 'A collaborative design tool for UI, UX, and prototyping',
                'description' => 'Figma is a collaborative design tool with AI-enhanced features for crafting digital interfaces.',
                'youtubeUrl' => 'https://www.youtube.com/watch?v=your_figma_video',
            ],
            [
                'id' => 10,
                'img' => asset('asset/img/learn/10.png'),
                'icon' => '🖼️',
                'title' => 'Midjourney',
                'subtitle' => 'An AI tool that creates artistic images from text prompts',
                'description' => 'Midjourney translates your text prompts into breathtaking artistic visuals using powerful AI.',
                'youtubeUrl' => 'https://www.youtube.com/watch?v=your_midjourney_video',
            ],
        ];
    }
}
