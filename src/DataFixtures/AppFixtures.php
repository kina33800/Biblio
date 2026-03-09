<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $mangas = [
            ['titre' => 'One Piece T110', 'auter' => 'Eiichiro Oda', 'isbn' => '9782344049101', 'stock' => 5, 'image' => 'https://picsum.photos/seed/onepiece/200/300'],
            ['titre' => 'One Piece T109', 'auter' => 'Eiichiro Oda', 'isbn' => '9782344049092', 'stock' => 4, 'image' => 'https://picsum.photos/seed/onepiece2/200/300'],
            ['titre' => 'Jujutsu Kaisen T26', 'auter' => 'Gege Akutami', 'isbn' => '9782820342607', 'stock' => 4, 'image' => 'https://picsum.photos/seed/jujutsu1/200/300'],
            ['titre' => 'Jujutsu Kaisen T25', 'auter' => 'Gege Akutami', 'isbn' => '9782820342591', 'stock' => 3, 'image' => 'https://picsum.photos/seed/jujutsu2/200/300'],
            ['titre' => 'My Hero Academia T40', 'auter' => 'Kohei Horikoshi', 'isbn' => '9782380714401', 'stock' => 3, 'image' => 'https://picsum.photos/seed/mha1/200/300'],
            ['titre' => 'My Hero Academia T39', 'auter' => 'Kohei Horikoshi', 'isbn' => '9782380714395', 'stock' => 2, 'image' => 'https://picsum.photos/seed/mha2/200/300'],
            ['titre' => 'Spy x Family T14', 'auter' => 'Tatsuya Endo', 'isbn' => '9782380715401', 'stock' => 4, 'image' => 'https://picsum.photos/seed/spy1/200/300'],
            ['titre' => 'Spy x Family T13', 'auter' => 'Tatsuya Endo', 'isbn' => '9782380715395', 'stock' => 3, 'image' => 'https://picsum.photos/seed/spy2/200/300'],
            ['titre' => 'Demon Slayer T23', 'auter' => 'Koyoharu Gotouge', 'isbn' => '9782820342300', 'stock' => 3, 'image' => 'https://picsum.photos/seed/demon1/200/300'],
            ['titre' => 'Chainsaw Man T17', 'auter' => 'Tatsuki Fujimoto', 'isbn' => '9782820341701', 'stock' => 4, 'image' => 'https://picsum.photos/seed/chainsaw1/200/300'],
            ['titre' => 'Dragon Ball Full Color T3', 'auter' => 'Akira Toriyama', 'isbn' => '9782344068861', 'stock' => 5, 'image' => 'https://picsum.photos/seed/dragonball1/200/300'],
            ['titre' => 'Naruto T72', 'auter' => 'Masashi Kishimoto', 'isbn' => '9782820347206', 'stock' => 3, 'image' => 'https://picsum.photos/seed/naruto1/200/300'],
            ['titre' => 'Attack on Titan T34', 'auter' => 'Hajime Isayama', 'isbn' => '9782820343406', 'stock' => 2, 'image' => 'https://picsum.photos/seed/aot1/200/300'],
            ['titre' => 'Frieren T12', 'auter' => 'Kanehito Yamada', 'isbn' => '9782380716201', 'stock' => 4, 'image' => 'https://picsum.photos/seed/frieren1/200/300'],
            ['titre' => 'Kaiju No. 8 T11', 'auter' => 'Naoya Matsumoto', 'isbn' => '9782820341106', 'stock' => 3, 'image' => 'https://picsum.photos/seed/kaiju1/200/300'],
            ['titre' => 'Blue Lock T28', 'auter' => 'Muneyuki Kaneshiro', 'isbn' => '9782820342805', 'stock' => 4, 'image' => 'https://picsum.photos/seed/bluelock1/200/300'],
            ['titre' => 'Vinland Saga T11', 'auter' => 'Makoto Yukimura', 'isbn' => '9782820341107', 'stock' => 2, 'image' => 'https://picsum.photos/seed/vinland1/200/300'],
            ['titre' => 'Kingdom T75', 'auter' => 'Yasuhisa Hara', 'isbn' => '9782344057506', 'stock' => 3, 'image' => 'https://picsum.photos/seed/kingdom1/200/300'],
            ['titre' => 'Tokyo Revengers T31', 'auter' => 'Ken Wakui', 'isbn' => '9782820343109', 'stock' => 2, 'image' => 'https://picsum.photos/seed/tokyo1/200/300'],
            ['titre' => 'Bleach T74', 'auter' => 'Tite Kubo', 'isbn' => '9782820347408', 'stock' => 3, 'image' => 'https://picsum.photos/seed/bleach1/200/300'],
            ['titre' => 'Hunter x Hunter T37', 'auter' => 'Yoshihiro Togashi', 'isbn' => '9782820343704', 'stock' => 5, 'image' => 'https://picsum.photos/seed/hxh1/200/300'],
            ['titre' => 'Berserk T42', 'auter' => 'Kentaro Miura', 'isbn' => '9782756084206', 'stock' => 2, 'image' => 'https://picsum.photos/seed/berserk1/200/300'],
            ['titre' => 'Fullmetal Alchemist T27', 'auter' => 'Hiromu Arakawa', 'isbn' => '9782723480277', 'stock' => 3, 'image' => 'https://picsum.photos/seed/fma1/200/300'],
            ['titre' => 'Death Note T13', 'auter' => 'Tsugumi Ohba', 'isbn' => '9782756054131', 'stock' => 4, 'image' => 'https://picsum.photos/seed/deathnote1/200/300'],
            ['titre' => 'Dandadan T12', 'auter' => 'Yukinobu Tatsu', 'isbn' => '9782380716121', 'stock' => 3, 'image' => 'https://picsum.photos/seed/dandadan1/200/300'],
            ['titre' => 'Dungeon Meshi T14', 'auter' => 'Ryoko Kui', 'isbn' => '9782380716140', 'stock' => 4, 'image' => 'https://picsum.photos/seed/dungeon1/200/300'],
            ['titre' => 'Gachiakuta T8', 'auter' => 'Hiroki Ozaki', 'isbn' => '9782380716084', 'stock' => 3, 'image' => 'https://picsum.photos/seed/gachiakuta1/200/300'],
            ['titre' => 'Solo Leveling T8', 'auter' => 'Chugong', 'isbn' => '9782380715089', 'stock' => 4, 'image' => 'https://picsum.photos/seed/solo1/200/300'],
            ['titre' => 'Vagabond T37', 'auter' => 'Takehiko Inoue', 'isbn' => '9782756084374', 'stock' => 2, 'image' => 'https://picsum.photos/seed/vagabond1/200/300'],
            ['titre' => 'One Punch Man T29', 'auter' => 'ONE / Yusuke Murata', 'isbn' => '9782820342904', 'stock' => 3, 'image' => 'https://picsum.photos/seed/opm1/200/300'],
        ];

        foreach ($mangas as $data) {
            $book = new Book();
            $book->setTitre($data['titre']);
            $book->setAuter($data['auter']);
            $book->setIsbn($data['isbn']);
            $book->setStock($data['stock']);
            $book->setImage($data['image']);
            $manager->persist($book);
        }

        $manager->flush();
    }
}