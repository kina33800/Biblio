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
            // On extrait juste le nom du manga sans le numéro de tome
            $titre = preg_replace('/\s+T\d+$/i', '', $book->getTitre());

            $io->text("Recherche cover pour : $titre");

            try {
                $response = $this->httpClient->request('GET', 'https://api.jikan.moe/v4/manga', [
                    'query' => ['q' => $titre, 'limit' => 1]
                ]);

                $data = $response->toArray();

                if (!empty($data['data'][0]['images']['jpg']['image_url'])) {
                    $imageUrl = $data['data'][0]['images']['jpg']['image_url'];
                    $book->setImage($imageUrl);
                    $io->success("Cover trouvée : $imageUrl");
                } else {
                    $io->warning("Aucune cover trouvée pour $titre");
                }

                // Pause pour ne pas surcharger l'API (limite : 3 req/sec)
                sleep(1);

            } catch (\Exception $e) {
                $io->error("Erreur pour $titre : " . $e->getMessage());
            }
        }

        $this->em->flush();
        $io->success('Toutes les covers ont été mises à jour !');

        return Command::SUCCESS;
    }
}