<?php

namespace App\DataFixtures;

use App\Entity\Airline;
use App\Entity\Flight;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // --- Admin ---
        $admin = new User();
        $admin->setEmail('admin@airadvisor.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPseudo('Admin');
        $admin->setFirstName('Admin');
        $admin->setLastName('AirAdvisor');
        $admin->setProfileSlug('admin');
        $manager->persist($admin);

        // --- 10 Users lambda ---
        $users = [$admin];
        $cities = ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Bordeaux', 'Nantes', 'Strasbourg', 'Lille', 'Montpellier'];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setPseudo($faker->userName());
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setDepartureCity($cities[$i]);
            $user->setProfileSlug($faker->unique()->slug(2));
            $manager->persist($user);
            $users[] = $user;
        }

        // --- 5 Airlines ---
        $airlinesData = [
            ['Air France', 'AF', 'France', 'air-france'],
            ['EasyJet', 'U2', 'Royaume-Uni', 'easyjet'],
            ['Ryanair', 'FR', 'Irlande', 'ryanair'],
            ['Lufthansa', 'LH', 'Allemagne', 'lufthansa'],
            ['British Airways', 'BA', 'Royaume-Uni', 'british-airways'],
        ];

        $airlines = [];
        foreach ($airlinesData as [$name, $iata, $country, $slug]) {
            $airline = new Airline();
            $airline->setName($name);
            $airline->setIataCode($iata);
            $airline->setCountry($country);
            $airline->setSlug($slug);
            $manager->persist($airline);
            $airlines[] = $airline;
        }

        // --- 20 Flights ---
        $destinations = [
            ['Londres', 'LHR'], ['Rome', 'FCO'], ['Barcelone', 'BCN'], ['Amsterdam', 'AMS'],
            ['Berlin', 'BER'], ['Lisbonne', 'LIS'], ['Madrid', 'MAD'], ['Athènes', 'ATH'],
            ['Dublin', 'DUB'], ['Vienne', 'VIE'], ['Prague', 'PRG'], ['Copenhague', 'CPH'],
            ['Stockholm', 'ARN'], ['Marrakech', 'RAK'], ['Istanbul', 'IST'], ['New York', 'JFK'],
            ['Milan', 'MXP'], ['Genève', 'GVA'], ['Bruxelles', 'BRU'], ['Porto', 'OPO'],
        ];
        $statuses = ['on_time', 'delayed', 'cancelled', 'landed', 'scheduled'];

        $flights = [];
        for ($i = 0; $i < 20; $i++) {
            $airline = $airlines[array_rand($airlines)];
            [$arrCity, $arrIata] = $destinations[$i];
            $date = $faker->dateTimeBetween('-6 months', '+1 month');
            $depHour = $faker->numberBetween(6, 22);
            $depTime = (clone $date)->setTime($depHour, $faker->randomElement([0, 15, 30, 45]));
            $arrTime = (clone $depTime)->modify('+' . $faker->numberBetween(1, 10) . ' hours');

            $flight = new Flight();
            $flight->setFlightNumber($airline->getIataCode() . $faker->numberBetween(100, 9999));
            $flight->setFlightIataCode($airline->getIataCode() . $faker->numberBetween(100, 9999));
            $flight->setDepartureCity('Paris');
            $flight->setDepartureIataCode('CDG');
            $flight->setArrivalCity($arrCity);
            $flight->setArrivalIataCode($arrIata);
            $flight->setFlightDate($date);
            $flight->setScheduledDepartureTime($depTime);
            $flight->setScheduledArrivalTime($arrTime);
            $flight->setStatus($statuses[array_rand($statuses)]);
            $flight->setAirline($airline);
            $manager->persist($flight);
            $flights[] = $flight;
        }

        // --- 50 Reviews ---
        $reviewTitles = [
            'Vol agréable dans l\'ensemble',
            'Retard inacceptable',
            'Excellent service à bord',
            'Très déçu par cette compagnie',
            'Rapport qualité-prix correct',
            'Personnel très aimable',
            'Siège inconfortable',
            'Atterrissage parfait, vol sans encombre',
            'Je recommande vivement',
            'À éviter absolument',
            'Bonne expérience malgré le retard',
            'Vol annulé sans prévenir',
            'Super vol pour le prix',
            'Nourriture immangeable',
            'Ponctualité exemplaire',
        ];

        $reviewContents = [
            'Le vol s\'est très bien passé, le personnel était souriant et disponible. Les sièges étaient confortables pour un vol court-courrier.',
            'Retard de plus de 2 heures sans aucune explication de la part de la compagnie. Très frustrant quand on a une correspondance.',
            'Service impeccable du début à la fin. L\'embarquement était rapide et bien organisé. Je reprendrai cette compagnie sans hésiter.',
            'Les sièges sont beaucoup trop étroits, impossible de dormir. Le repas servi était froid et sans goût.',
            'Pour le prix payé, c\'est honnête. Rien d\'exceptionnel mais le vol était à l\'heure et les bagages sont arrivés rapidement.',
            'Le commandant de bord a pris le temps de nous informer de la météo et des conditions de vol. Très professionnel.',
            'L\'avion était sale, les tablettes collantes. J\'ai signalé le problème mais personne n\'a réagi.',
            'Vol parfait, décollage et atterrissage en douceur. Le divertissement à bord était varié.',
            'Première fois avec cette compagnie et je suis conquis. Le check-in en ligne est très simple.',
            'Bagages arrivés avec 24h de retard. Le service client est injoignable. Plus jamais.',
        ];

        for ($i = 0; $i < 50; $i++) {
            $rating = $faker->numberBetween(1, 5);

            $review = new Review();
            $review->setTitle($reviewTitles[array_rand($reviewTitles)]);
            $review->setContent($reviewContents[array_rand($reviewContents)]);
            $review->setRating($rating);
            $review->setRatingComfort($faker->numberBetween(max(1, $rating - 1), min(5, $rating + 1)));
            $review->setRatingPunctuality($faker->numberBetween(max(1, $rating - 1), min(5, $rating + 1)));
            $review->setRatingStaff($faker->numberBetween(max(1, $rating - 1), min(5, $rating + 1)));
            $review->setRatingFood($faker->numberBetween(1, 5));
            $review->setAuthor($users[array_rand($users)]);
            $review->setFlight($flights[array_rand($flights)]);
            $manager->persist($review);
        }

        $manager->flush();
    }
}
