<?php

namespace App\Command;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'app:fetch-covers')]
class FetchCoversCommand extends Command
{
    public function __construct(
        private BookRepository $bookRepository,
        private EntityManagerInterface $em,
        private HttpClientInterface $httpClient
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $books = $this->bookRepository->findAll();

        foreach ($books as $book) {
            $titre = preg_replace('/\s+T\d+$/i', '', $book->getTitre());
            $io->text("Recherche pour : $titre");

            try {
                $response = $this->httpClient->request('GET', 'https://api.jikan.moe/v4/manga', [
                    'query' => ['q' => $titre, 'limit' => 1]
                ]);

                $data = $response->toArray();

                if (!empty($data['data'][0])) {
                    $manga = $data['data'][0];

                    // Cover
                    if (!empty($manga['images']['jpg']['large_image_url'])) {
                        $book->setImage($manga['images']['jpg']['large_image_url']);
                        $io->text("✅ Cover : " . $manga['images']['jpg']['large_image_url']);
                    }

                    // Aperçu — on prend le synopsis comme texte d'accroche
                    if (!empty($manga['synopsis'])) {
                        $synopsis = mb_substr($manga['synopsis'], 0, 300) . '...';
                        $book->setAperçu($synopsis);
                        $io->text("✅ Synopsis récupéré");
                    }
                }

                sleep(1);

            } catch (\Exception $e) {
                $io->error("Erreur pour $titre : " . $e->getMessage());
            }
        }

        $this->em->flush();
        $io->success('Covers et aperçus mis à jour !');

        return Command::SUCCESS;
    }
}