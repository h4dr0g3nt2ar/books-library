<?php

declare(strict_types=1);

namespace App\Domain\Book;

use App\Domain\Book\Enum\BindingType;

final class Book
{
    private function __construct(
        private int $id,
        private string $title,
        private int $year,
        private ?string $description,
        private array $authorIds,

        private ?BindingType $binding,      // переплет
        private ?int $pages,                // кол-во страниц
        private ?int $printRun,             // тираж
        private ?string $isbn,

        private ?int $countryId,
        private ?int $publisherId,
        private ?int $seriesId,

        private array $translatorIds,

        private array $tagIds,

        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt
    ) {
        self::validateTitle($title);
        self::validateYear($year);
        self::validateDescription($description);
        self::validateAuthorIds($authorIds);
        self::validatePages($pages);
        self::validatePrintRun($printRun);
        self::validateIsbn($isbn);
        self::validateCountryId($countryId);
        self::validatePublisherId($publisherId);
        self::validateSeriesId($seriesId);
        self::validateTranslatorIds($translatorIds);
        self::validateTagIds($tagIds);

    }
    public static function create(
        string $title,
        int $year,
        ?string $description = null,

        array $authorIds = [],

        ?BindingType $binding = null,
        ?int $pages = null,
        ?int $printRun = null,
        ?string $isbn = null,

        ?int $countryId = null,
        ?int $publisherId = null,
        ?int $seriesId = null,

        array $translatorIds= [],
        array $tagIds = []
    ): self{

        $now = new \DateTimeImmutable();

        return new self(
            id: 0,
            title: \trim($title),
            year: $year,
            description: $description,

            authorIds: $authorIds,

            binding: $binding,
            pages: $pages,
            printRun: $printRun,
            isbn: $isbn,

            countryId: $countryId,
            publisherId: $publisherId,
            seriesId: $seriesId,

            translatorIds: $translatorIds,
            tagIds: $tagIds,

            createdAt: $now,
            updatedAt: $now
        );
    }
    public static function reconstitute(
        int $id,
        string $title,
        int $year,
        ?string $description,

        array $authorIds,

        ?BindingType $binding,
        ?int $pages,
        ?int $printRun,
        ?string $isbn,

        ?int $countryId,
        ?int $publisherId,
        ?int $seriesId,

        array $translatorIds,
        array $tagIds,

        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self{
        return new self(
            id: $id,
            title: $title,
            year: $year,
            description: $description,

            authorIds:$authorIds,

            binding: $binding,
            pages: $pages,
            printRun: $printRun,
            isbn: $isbn,

            countryId: $countryId,
            publisherId: $publisherId,
            seriesId: $seriesId,

            translatorIds: $translatorIds,
            tagIds: $tagIds,

            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }
    private function validateTitle(string $title): void{
        if(\trim($title) === ''){
            throw new \InvalidArgumentException(
                'Title cannot be empty'
            );
        }
        if(\mb_strlen($title, 'UTF-8') > 200){
            throw new \InvalidArgumentException(
                'Title too long (max 200 chars)'
            );
        }
    }
    private function validateYear(int $year): void{
        if($year < 1000){
            throw new \InvalidArgumentException(
                'Year must be 1000 or later'
            );
        }
        $currentYear = (int)\date('Y');
        if($year > $currentYear){
            throw new \InvalidArgumentException(
                'Year cannot be more than '. $currentYear
            );
        }
    }
    private function validateDescription(?string $description): void{
        if ($description === null) {
            return;
        }
        if(\mb_strlen($description, 'UTF-8') > 10000){
            throw new \InvalidArgumentException(
                'Description too long'
            );
        }
    }
    private function validateAuthorIds(array $authorIds): void{
        foreach($authorIds as $authorId){
            if(!\is_int($authorId)){
                throw new \InvalidArgumentException(
                    'Each author ID must be an integer'
                );
            }
            if($authorId <= 0){
                throw new \InvalidArgumentException(
                    'Author id must be positive. You tried to fill: ' . $authorId
                );
            }
        }
        if(\count($authorIds) !== \count(array_unique($authorIds))){
            throw new \InvalidArgumentException(
                'Duplicate author Ids'
            );
        }
    }
    private function validatePages(?int $pages): void{
        if($pages === null){
            return;
        }
        if($pages <= 0){
            throw new \InvalidArgumentException(
                'Pages must be positive. You tried to fill: ' . $pages
            );
        }
    }
    private function validatePrintRun(?int $printRun): void{
        if($printRun === null){
            return;
        }
        if($printRun <= 0){
            throw new \InvalidArgumentException(
                'Print run must be positive. You tried to fill: ' . $printRun
            );
        }
    }
    private static function validateIsbn(?string $isbn): void
    {
        if ($isbn === null) {
            return;
        }

        // Убираем дефисы и пробелы для проверки
        $clean = \preg_replace('/[\s\-]/', '', $isbn);

        // Должен быть 10 или 13 цифр (последний символ может быть X для ISBN-10)
        if (!\preg_match('/^(\d{9}[\dX]|\d{13})$/i', $clean)) {
            throw new \InvalidArgumentException('Invalid ISBN format');
        }
    }
    private static function validateCountryId(?int $countryId): void{
        if($countryId !== null && $countryId <= 0 ){
            throw new \InvalidArgumentException('Country id must be positive. You tried to fill: ' . $countryId);
        }
    }
    private static function validatePublisherId(?int $publisherId): void{
        if($publisherId !== null && $publisherId <= 0 ){
            throw new \InvalidArgumentException('Publisher id must be positive. You tried to fill: ' . $publisherId);
        }
    }
    private static function validateSeriesId(?int $seriesId): void{
        if($seriesId !== null && $seriesId <= 0 ){
            throw new \InvalidArgumentException('Series id must be positive. You tried to fill: ' . $seriesId);
        }
    }

    private function validateTranslatorIds(array $translatorIds): void{
        foreach($translatorIds as $translatorId){
            if(!\is_int($translatorId)){
                throw new \InvalidArgumentException(
                    'Each translator ID must be an integer'
                );
            }
            if($translatorId <= 0){
                throw new \InvalidArgumentException(
                    'Translator id must be positive. You tried to fill: ' . $translatorId
                );
            }
        }
        if(\count($translatorIds) !== \count(array_unique($translatorIds))){
            throw new \InvalidArgumentException(
                'Duplicate translator Ids'
            );
        }
    }
    private function validateTagIds(array $tagIds): void{
        foreach($tagIds as $tagId){
            if(!\is_int($tagId)){
                throw new \InvalidArgumentException(
                    'Each tag ID must be an integer'
                );
            }
            if($tagId <= 0){
                throw new \InvalidArgumentException(
                    'Tag id must be positive. You tried to fill: ' . $tagId
                );
            }
        }
        if(\count($tagIds) !== \count(array_unique($tagIds))){
            throw new \InvalidArgumentException(
                'Duplicate tag Ids'
            );
        }
    }

    public function id(): int{
        return $this->id;
    }
    public function title(): string{
        return $this->title;
    }
    public function year(): int{
        return $this->year;
    }
    public function description(): ?string{
        return $this->description;
    }
    public function authorIds(): array{
        return $this->authorIds;
    }
    public function binding(): ?BindingType{
        return $this->binding;
    }
    public function pages(): ?int{
        return $this->pages;
    }
    public function printRun(): ?int{
        return $this->printRun;
    }
    public function isbn(): ?string{
        return $this->isbn;
    }
    public function countryId(): ?int{
        return $this->countryId;
    }
    public function publisherId(): ?int{
        return $this->publisherId;
    }
    public function seriesId(): ?int{
        return $this->seriesId;
    }
    public function translatorIds(): array{
        return $this->translatorIds;
    }
    public function tagIds(): array{
        return $this->tagIds;
    }
    public function createdAt(): \DateTimeImmutable{
        return $this->createdAt;
    }
    public function updatedAt(): \DateTimeImmutable{
        return $this->updatedAt;
    }
    public function isNew(): bool{
        return $this->id === 0;
    }

    public function rename(string $newTitle): void{
        $this->validateTitle($newTitle);
        $this->title = $newTitle;

        $this->updatedAt= new \DateTimeImmutable();
    }
    public function changeYear(int $newYear): void{
        $this->validateYear($newYear);
        $this->year = $newYear;

        $this->updatedAt= new \DateTimeImmutable();
    }
    public function changeDescription(?string $newDescription): void{
        $this->validateDescription($newDescription);
        $this->description = $newDescription;

        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changeBinding(?BindingType $newBinding): void{
        $this->binding = $newBinding;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changePages(?int $newPages): void{
        $this->validatePages($newPages);
        $this->pages = $newPages;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changePrintRun(?int $newPrintRun): void{
        $this->validatePrintRun($newPrintRun);
        $this->printRun = $newPrintRun;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changeIsbn(?string $newIsbn): void{
        $this->validateIsbn($newIsbn);
        $this->isbn = $newIsbn;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changeCountry(?int $newCountryId): void{
        $this->validateCountryId($newCountryId);
        $this->countryId = $newCountryId;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changePublisher(?int $newPublisherId): void{
        $this->validatePublisherId($newPublisherId);
        $this->publisherId = $newPublisherId;
        $this->updatedAt = new \DateTimeImmutable();
    }
    public function changeSeries(?int $newSeriesId): void{
        $this->validateSeriesId($newSeriesId);
        $this->seriesId = $newSeriesId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function addAuthor(int $authorId): void{
        if($authorId <= 0){
            throw new \InvalidArgumentException('Author ID must be positive');
        }
        if (!$this->hasAuthor($authorId)) {
            $this->authorIds[] = $authorId;
        }else{
            throw new \InvalidArgumentException('Author is already added');
        }

        $this->updatedAt = new \DateTimeImmutable();

    }
    public function removeAuthor(int $authorId): void{

        $removingAuthorKey = array_search($authorId, $this->authorIds);

        if($removingAuthorKey !== false){
            unset($this->authorIds[$removingAuthorKey]);
            $this->updatedAt= new \DateTimeImmutable();
        }
        $this->authorIds = \array_values($this->authorIds);


    }
    public function hasAuthor(int $authorId): bool{
        return \in_array($authorId, $this->authorIds, true);
    }

    public function addTranslator(int $translatorId): void{
        if($translatorId <= 0){
            throw new \InvalidArgumentException('Translator ID must be positive');
        }
        if (!$this->hasTranslator($translatorId)) {
            $this->translatorIds[] = $translatorId;
        }else{
            return;
        }

        $this->updatedAt = new \DateTimeImmutable();

    }
    public function removeTranslator(int $translatorId): void{

        $removingTranslatorKey = array_search($translatorId, $this->translatorIds);

        if($removingTranslatorKey !== false){
            unset($this->translatorIds[$removingTranslatorKey]);
            $this->updatedAt= new \DateTimeImmutable();
        }
        $this->translatorIds = \array_values($this->translatorIds);


    }
    public function hasTranslator(int $translatorId): bool{
        return \in_array($translatorId, $this->translatorIds, true);
    }
    public function addTag(int $tagId): void{
        if($tagId <= 0){
            throw new \InvalidArgumentException('Tag ID must be positive');
        }
        if (!$this->hasTag($tagId)) {
            $this->tagIds[] = $tagId;
        }else{
            return;
        }

        $this->updatedAt = new \DateTimeImmutable();

    }
    public function removeTag(int $tagId): void{

        $removingTagKey = array_search($tagId, $this->tagIds);

        if($removingTagKey !== false){
            unset($this->tagIds[$removingTagKey]);
            $this->updatedAt = new \DateTimeImmutable();
        }
        $this->tagIds = \array_values($this->tagIds);


    }
    public function hasTag(int $tagId): bool{
        return \in_array($tagId, $this->tagIds, true);
    }

    public function assignId(int $id): void{

        if(!$this->isNew()){
            throw new \RuntimeException('Cannot assign id to already saved entity');
        }
        if($id <= 0){
            throw new \InvalidArgumentException('ID must be positive');
        }

        $this->id = $id;
    }

}