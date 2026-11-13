<?php

declare(strict_types=1);

namespace App\Presentation\Controller;
use App\Domain\Country\Country;
use App\Infrastructure\Database\PdoCountryRepository;
final class CountryController
{
    public function __construct(
        private readonly PdoCountryRepository $countryRepo
    ){ }
    public function index(): void{
        $countries = $this->countryRepo->all();
        $total = $this->countryRepo->count();

        $title = 'Список стран';
        $data = [
            'countries' => $countries,
            'title' => $title,
            'total' => $total,
        ];

        $content = render('country/index', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function create(): void{
        $title = 'Добавить страну';
        $data = [
            'country' => null,
            'title' => $title,
            'errors' => []
        ];

        $content = render('country/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function store(): void{

        $title = $_POST['name'] ?? '';

        $errors = [];

        try {
            $country = Country::create(
                name: $title,
            );
        }catch (\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Добавить страну';

            $data = [
                'country' => null,
                'title' => $title,
                'errors' => $errors,
                'old' => $_POST,
            ];
            $content = render('country/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }
        $this->countryRepo->save($country);
        header('Location: /countries');

        exit;
    }
    public function edit(int $id): void{
        $country = $this->countryRepo->findById($id);
        if ($country === null) {
            http_response_code(404);
            echo '<h1>Страна не найдена</h1>';
            echo '<a href="/countries">К списку стран</a>';
            return;
        }
        $title = 'Редактировать страну';

        $data = [
            'country' => $country,
            'title' => $title,
            'errors' => []
        ];

        $content = render('country/form', $data);

        require BASE_PATH . '/src/Presentation/View/layout.php';
    }
    public function update(int $id): void{
        $country = $this->countryRepo->findById($id);
        if ($country === null) {
            http_response_code(404);
            echo '<h1>Страна не найдена</h1>';
            echo '<a href="/countries">К списку стран</a>';
            return;
        }
        $name = trim($_POST['name'] ?? '');
        $errors = [];

        try{
            $country->rename($name);
        }catch(\InvalidArgumentException $e){
            $errors[] = $e->getMessage();
        }
        if ($errors !== []) {
            $title = 'Редактировать страну';

            $data = [
                'country' => $country,
                'title' => $title,
                'errors' => $errors
            ];

            $content = render('country/form', $data);

            require BASE_PATH . '/src/Presentation/View/layout.php';

            return;
        }else{
            $this->countryRepo->save($country);
            header('Location: /countries'); exit;
        }
    }
    public function delete(int $id): void{
        $country = $this->countryRepo->findById($id);
        if ($country === null) {
            http_response_code(404);
            echo '<h1>Страна не найдена</h1>';
            echo '<a href="/countries">К списку стран</a>';
            return;
        }
        $this->countryRepo->delete($country);
        header('Location: /countries');
        exit;
    }
}