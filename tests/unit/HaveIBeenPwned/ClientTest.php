<?php
//
//declare(strict_types=1);
//
//namespace Itineris\DisallowPwnedPasswords\Test\HaveIBeenPwned;
//
//use Codeception\Test\Unit;
//use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Client;
//use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
//use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;
//use stdClass;
//use WP_Mock;
//
//class ClientTest extends Unit
//{
//    /**
//     * @var \Itineris\DisallowPwnedPasswords\Test\UnitTester
//     */
//    protected $tester;
//
//    public function testImplementsClientInterface()
//    {
//        $client = new Client();
//
//        $this->assertInstanceOf(ClientInterface::class, $client);
//    }
//
//    public function testGetPwnedTimes()
//    {
//        $response = [
//            'body' => "ABC:123\nXYZ:456\n6622A23D08DAFACC8A11115A3CFC148E51D:279\n",
//        ];
//
//        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_remote_get')
//            ->with('https://api.pwnedpasswords.com/range/486B5')
//            ->andReturn($response)
//            ->once();
//
//        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_remote_retrieve_response_code')
//               ->with($response)
//               ->andReturn(200)
//               ->once();
//
//        $client = new Client();
//        $password = new Password('password&');
//
//        $actual = $client->getPwnedTimes($password);
//
//        $this->assertSame(279, $actual);
//    }
//
//    public function testGetPwnedTimesUnreachable()
//    {
//        $response = new stdClass();
//
//        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_remote_get')
//               ->with('https://api.pwnedpasswords.com/range/486B5')
//               ->andReturn($response)
//               ->once();
//
//        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_remote_retrieve_response_code')
//               ->with($response)
//               ->andReturn(500)
//               ->once();
//
//        $client = new Client();
//        $password = new Password('password&');
//
//        $actual = $client->getPwnedTimes($password);
//
//        $this->assertNull($actual);
//    }
//
//    public function testFetchAndDecode()
//    {
//        $response = [
//            'body' => "ABC:123\nXYZ:456\n6622A23D08DAFACC8A11115A3CFC148E51D:279\n",
//        ];
//
//        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_remote_get')
//               ->with('https://api.pwnedpasswords.com/range/486B5')
//               ->andReturn($response)
//               ->once();
//
//        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_remote_retrieve_response_code')
//               ->with($response)
//               ->andReturn(200)
//               ->once();
//
//        $client = new Client();
//        $password = new Password('password&');
//
//        $actual = $client->fetchAndDecode($password);
//
//        $expected = [
//            'ABC' => 123,
//            'XYZ' => 456,
//            '6622A23D08DAFACC8A11115A3CFC148E51D' => 279,
//        ];
//
//        $this->assertSame($expected, $actual);
//    }
//
//    public function testFetchAndDecodeUnreachable()
//    {
//        $response = new stdClass();
//
//        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_remote_get')
//               ->with('https://api.pwnedpasswords.com/range/486B5')
//               ->andReturn($response)
//               ->once();
//
//        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_remote_retrieve_response_code')
//               ->with($response)
//               ->andReturn(500)
//               ->once();
//
//        $client = new Client();
//        $password = new Password('password&');
//
//        $actual = $client->fetchAndDecode($password);
//
//        $this->assertNull($actual);
//    }
//}
