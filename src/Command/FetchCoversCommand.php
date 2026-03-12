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
            $titreComplet = $book->getTitre();
            $titreSansVolume = preg_replace('/\s+T\d+$/i', '', $titreComplet);

            // Extraire le numéro de tome
            preg_match('/T(\d+)$/i', $titreComplet, $matches);
            $volume = $matches[1] ?? null;

            $io->text("🔍 Recherche : $titreComplet (Volume: $volume)");

            try {
                // Étape 1 — Chercher le manga sur MangaDex
                $searchResponse = $this->httpClient->request('GET', 'https://api.mangadex.org/manga', [
                    'query' => [
                        'title'                => $titreSansVolume,
                        'limit'                => 1,
                        'contentRating[]'      => 'safe',
                        'availableTranslatedLanguage[]' => 'fr',
                    ]
                ]);

                $searchData = $searchResponse->toArray();

                if (empty($searchData['data'][0])) {
                    // Retry sans filtre de langue
                    $searchResponse = $this->httpClient->request('GET', 'https://api.mangadex.org/manga', [
                        'query' => ['title' => $titreSansVolume, 'limit' => 1]
                    ]);
                    $searchData = $searchResponse->toArray();
                }

                if (!empty($searchData['data'][0])) {
                    $mangaId = $searchData['data'][0]['id'];
                    $io->text("   → MangaDex ID : $mangaId");

                    sleep(1);

                    // Étape 2 — Chercher la cover du bon volume
                    $coverQuery = [
                        'manga[]' => $mangaId,
                        'limit'   => 100,
                        'order[volume]' => 'asc',
                    ];

                    if ($volume) {
                        $coverQuery['volume[]'] = (string)(int)$volume;
                    }

                    // Étape 2 — Récupérer toutes les covers et filtrer par volume
                    $coverResponse = $this->httpClient->request('GET', 'https://api.mangadex.org/cover', [
                        'query' => [
                            'manga[]'        => $mangaId,
                            'limit'          => 100,
                            'order[volume]'  => 'asc',
                        ]
                    ]);

                    $coverData = $coverResponse->toArray();

                    $coverFound = false;
                    if (!empty($coverData['data'])) {
                        // Chercher la cover du bon volume
                        foreach ($coverData['data'] as $cover) {
                            $coverVolume = $cover['attributes']['volume'] ?? null;
                            $fileName    = $cover['attributes']['fileName'];

                            if ($volume && (string)(int)$coverVolume === (string)(int)$volume) {
                                $imageUrl = "https://uploads.mangadex.org/covers/$mangaId/$fileName";
                                $book->setImage($imageUrl);
                                $io->success("✅ Cover V$coverVolume trouvée");
                                $coverFound = true;
                                break;
                            }
                        }

                        // Fallback — première cover disponible
                        if (!$coverFound) {
                            $fileName = $coverData['data'][0]['attributes']['fileName'];
                            $imageUrl = "https://uploads.mangadex.org/covers/$mangaId/$fileName";
                            $book->setImage($imageUrl);
                            $io->warning("⚠️ Cover fallback utilisée");
                        }
                    }

                    // Étape 3 — Synopsis depuis Jikan
                    sleep(1);
                    $jikanResponse = $this->httpClient->request('GET', 'https://api.jikan.moe/v4/manga', [
                        'query' => ['q' => $titreSansVolume, 'limit' => 1]
                    ]);
                    $jikanData = $jikanResponse->toArray();
                    if (!empty($jikanData['data'][0]['synopsis'])) {
                        $book->setAperçu(mb_substr($jikanData['data'][0]['synopsis'], 0, 300) . '...');
                        $io->text("   ✅ Synopsis récupéré");
                    }

                } else {
                    $io->warning("Aucun résultat MangaDex pour : $titreSansVolume");
                }

                sleep(1);

            } catch (\Exception $e) {
                $io->error("Erreur pour $titreComplet : " . $e->getMessage());
            }
        }

        $this->em->flush();
        $io->success('✅ Terminé !');

        return Command::SUCCESS;
    }
}