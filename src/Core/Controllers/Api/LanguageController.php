<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Localisation\LanguageRepositoryInterface;
use App\Core\Http\ApiController;


class LanguageController extends ApiController
{
    private LanguageRepositoryInterface $languageRepository;

    public function __construct(
        LanguageRepositoryInterface $languageRepository,
    )
    {
        parent::__construct();
        $this->languageRepository = $languageRepository;
    }

    /**
     * Create a new client.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $languages = $this->languageRepository->findAll();
        return $this->renderResponse($languages);
    }

    /**
     * Show a language.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {

        $language = $this->languageRepository->find((int)$id);
        if(!$language){
            return $this->response->withStatus(404)->withBody('Language not found');
        }
        return $this->renderResponse($language->data);
    }

    /**
     * Create a new language.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'code' => 'required|string',
                'locale' => 'required|string',
                'rtl' => 'required|int',
                'sort_order' => 'required|int',
                'status' => 'required|int',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        if ($this->languageRepository->findOneBy(['code' => $data['code']])) {
            // return $this->renderError(400, 'Code is already in use.', ['code' => 'The language code '.$data['code']." is already in use."]);
            throw new ValidationException(['code' => ['Language code is already in use.']]);
        }

        $language = $this->languageRepository->create($data);
        return $this->renderResponse($language->data);
    }

    /**
     * Update a language.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'code' => 'string|nullable',
                'locale' => 'string|nullable',
                'rtl' => 'int|nullable',
                'sort_order' => 'int|nullable',
                'status' => 'int|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        // Check if language exists
        $language = $this->languageRepository->find((int)$id);
        if (!$language) {
            return $this->renderError(404, 'Language not found');
        }

        // Check for duplicate code if code is being updated
        if (isset($data['code'])) {
            $existingLanguage = $this->languageRepository->get(null, $data['code']);
            if ($existingLanguage && $existingLanguage->language_id != $id) {
                throw new ValidationException(['code' => ['Language code is already in use.']]);
            }
        }

        // Update the language
        $updated = $this->languageRepository->update((int)$id, $data);
        if (!$updated) {
         throw new ValidationException(['code' => ['Failed to update language.']]);
        }

        // Get the updated language
        $updatedLanguage = $this->languageRepository->find((int)$id);
        return $this->renderResponse($updatedLanguage->data);
    }

    /**
     * Delete a language.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->languageRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Language deleted successfully']);
    }
    
    
}
