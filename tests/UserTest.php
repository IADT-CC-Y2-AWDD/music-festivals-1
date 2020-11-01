<?php declare(strict_types=1);

require_once "config.php";

use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase {

  protected function setUp() : void {
  }

  public function testFindAll() : void {
    $users = User::findAll();
    $this->assertNotNull($users);
    $this->assertIsArray($users);
  }

  public function testFindById() : void {
    $user = User::findById(1);
    $this->assertNotNull($user);
    $this->assertInstanceOf(User::class, $user);
    $this->assertEquals($user->id, 1);

    $user = User::findById(0);
    $this->assertNull($user);
  }

  public function testFindByEmail() : void {
    $user = User::findByEmail("francis@bloggs.com");
    $this->assertNotNull($user);
    $this->assertInstanceOf(User::class, $user);
    $this->assertEquals($user->email, "francis@bloggs.com");

    $user = User::findByEmail("banana@abc.com");
    $this->assertNull($user);
  }

  public function testSaveInsert() : void {
    $user = new User();
    $user->email = "test@dummy.com";
    $user->password = password_hash('mysecret', PASSWORD_DEFAULT);
    $user->name = "Test Dummy";
    $user->save();
    $this->assertNotNull($user->id);

    $found_user = User::findById($user->id);
    $this->assertNotNull($found_user);
    $this->assertEquals($found_user->id, $user->id);
    $this->assertEquals($found_user->email, $user->email);
    $this->assertEquals($found_user->password, $user->password);
    $this->assertEquals($found_user->name, $user->name);

    $this->expectException(PDOException::class);
    $user = new User();
    $user->email = "test@dummy.com";
    $user->password = password_hash('mysecret', PASSWORD_DEFAULT);
    $user->name = "Test Dummy";
    $user->save();
  }

   public function testSaveUpdate() : void {
     $user = User::findByEmail("test@dummy.com");
     $this->assertNotNull($user);
     $user->email = "test_update@dummy.com";
     $user->password = password_hash('my_new_secret', PASSWORD_DEFAULT);
     $user->name = "Test Update Dummy";
     $user->save();

     $found_user = User::findById($user->id);
     $this->assertNotNull($found_user);
     $this->assertEquals($found_user->id, $user->id);
     $this->assertEquals($found_user->email, $user->email);
     $this->assertEquals($found_user->password, $user->password);
     $this->assertEquals($found_user->name, $user->name);
   }

  public function testDelete() : void {
    $user = User::findByEmail("test_update@dummy.com");
    $this->assertNotNull($user);

    $users = User::findAll();
    $this->assertIsArray($users);
    $current_num_users = count($users);

    $user->delete();

    $user = User::findByEmail("test_update@dummy.com");
    $this->assertNull($user);
  }

  protected function tearDown() : void {
  }
}
