<?php

namespace Modules\Core\Internationalisation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Set the translation key prefix for attributes.
     * @var string
     */
    protected $translationsAttributesKey = 'validation.attributes.';
    /**
     * Current processed locale
     * @var string
     */
    protected $localeKey;

    /**
     * Return an array of rules for translatable fields
     * @return array
     */
    public function translationRules()
    {
        return [];
    }

    /**
     * Return an array of messages for translatable fields
     * @return array
     */
    public function translationMessages()
    {
        return [];
    }

    /**
     * @return array
     */
    public function withTranslations()
    {
        $results = $this->all();
        $translations = [];
        foreach ($this->requiredLocales() as $key => $locale) {
            $locales[] = $key;
            $translations[$key] = $this->get($key);
        }
        $results['translations'] = $translations;
        Arr::forget($results, $locales);

        return $results;
    }

    /**
     * Get the validator instance for the request.
     * @return Validator
     */
    protected function getValidatorInstance()
    {
        $factory = $this->container->make('Illuminate\Validation\Factory');
        if (method_exists($this, 'validator')) {
            return $this->container->call([$this, 'validator'], compact('factory'));
        }

        $rules = $this->container->call([$this, 'rules']);
        $attributes = $this->attributes();
        $messages = [];

        $translationsAttributesKey = $this->getTranslationsAttributesKey();

        foreach ($this->requiredLocales() as $localeKey => $locale) {
            $this->localeKey = $localeKey;
            foreach ($this->container->call([$this, 'translationRules']) as $attribute => $rule) {
                $key = $localeKey . '.' . $attribute;
                $rules[$key] = $rule;
                $attributes[$key] = trans($translationsAttributesKey . $attribute);
            }

            foreach ($this->container->call([$this, 'translationMessages']) as $attributeAndRule => $message) {
                $messages[$localeKey . '.' . $attributeAndRule] = $message;
            }
        }

        return $factory->make(
            $this->all(),
            $rules,
            array_merge($this->messages(), $messages),
            $attributes
        );
    }

    /**
     * Get the validation for attributes key from the implementing class
     * or use a sensible default
     * @return string
     */
    private function getTranslationsAttributesKey()
    {
        return rtrim($this->translationsAttributesKey, '.') . '.';
    }

    /**
     * @return Collection
     */
    public function requiredLocales()
    {
        return LaravelLocalization::getSupportedLocales();
    }
}
