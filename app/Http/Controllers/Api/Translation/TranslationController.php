<?php

namespace App\Http\Controllers\Api\Translation;

use App\Http\Controllers\Controller;
use App\Models\Translation\Translation;
use BabyMarkt\DeepL\DeepL;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function translate($text, $sourceLang, $targetLang)
    {
        $text = trim($text);

        $translation = Translation::where($sourceLang, $text)->first();

        if($translation) {
            if($translation->$targetLang !== null) {
                $translatedText = $translation->$targetLang;
            }
            else {
                $deepl   = new DeepL(env('DEEPL_KEY'),2, env('DEEPL_HOST'));

                $response =  $deepl->translate($text, $sourceLang, $targetLang);

                $translatedText = $response[0]['text'];

                $translation->update([
                    $targetLang => $translatedText,
                    'translation_type_'.$targetLang => 'deepl'
                ]);
            }
        }
        else {
            $deepl   = new DeepL(env('DEEPL_KEY'),2, env('DEEPL_HOST'));

            $response =  $deepl->translate($text, $sourceLang, $targetLang);

            $translatedText = $response[0]['text'];

            Translation::create([
                $sourceLang => $text,
                $targetLang => $translatedText,
                'translation_type_'.$sourceLang => 'original',
                'translation_type_'.$targetLang => 'deepl'
            ]);
        }

        return  $translatedText;
    }

    public function postmanTranslate(Request $request)
    {
        $payload = json_decode($request->getContent());

        $translation = Translation::where($payload->sourceLang, trim($payload->text))->first();

        $targetLang = $payload->targetLang;

        if($translation) {
            if($translation->$targetLang !== null) {
                $translatedText = $translation->$targetLang;
            }
            else {
                $deepl   = new DeepL(env('DEEPL_KEY'),2,env('DEEPL_HOST'));

                $response =  $deepl->translate($payload->text, $payload->sourceLang, $payload->targetLang);

                $translatedText = $response[0]['text'];

                $translation->update([
                    $payload->targetLang => $translatedText,
                    'translation_type_'. $payload->targetLang  => 'deepl'
                ]);
            }

        }
        else {
            $deepl   = new DeepL(env('DEEPL_KEY'),2,env('DEEPL_HOST'));

            $response =  $deepl->translate($payload->text, $payload->sourceLang, $payload->targetLang);

            $translatedText = $response[0]['text'];

            Translation::create([
                $payload->sourceLang => $payload->text,
                $payload->targetLang => $translatedText,
                'translation_type_'.$payload->sourceLang  => 'original',
                'translation_type_'. $payload->targetLang  => 'deepl'
            ]);
        }

        return  $translatedText;
    }
}
