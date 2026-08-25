

namespace Tests\Feature\Book;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはマイリストにアクセスできずログインへリダイレクトされる()
    {
        $response = $this->get(route('mylist.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーはマイリストにアクセスできる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('mylist.index'));

        $response->assertStatus(200);
        $response->assertSee('マイリスト'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function ログインユーザーのお気に入り書籍だけが表示される()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $book1 = Book::factory()->create(['title' => 'ユーザーの本']);
        $book2 = Book::factory()->create(['title' => '他人の本']);

        // ユーザーのお気に入り
        $book1->favorites()->attach($user->id);

        // 他人のお気に入り
        $book2->favorites()->attach($other->id);

        $response = $this->actingAs($user)->get(route('mylist.index'));

        $response->assertSee('ユーザーの本');
        $response->assertDontSee('他人の本');
    }

    /** @test */
    public function 検索キーワードが保持される()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('mylist.index', ['keyword' => 'Laravel']));

        $response->assertSee('value="Laravel"'); // <input value="Laravel">
    }

    /** @test */
    public function 検索結果が正しく絞られる()
    {
        $user = User::factory()->create();

        $book1 = Book::factory()->create(['title' => 'Laravel入門']);
        $book2 = Book::factory()->create(['title' => 'PHPの本']);

        $book1->favorites()->attach($user->id);
        $book2->favorites()->attach($user->id);

        $response = $this->actingAs($user)->get(route('mylist.index', ['keyword' => 'Laravel']));

        $response->assertSee('Laravel入門');
        $response->assertDontSee('PHPの本');
    }

    /** @test */
    public function マイリストは10件でページネーションされる()
    {
        $user = User::factory()->create();

        // 15件お気に入り登録
        $books = Book::factory()->count(15)->create();
        foreach ($books as $book) {
            $book->favorites()->attach($user->id);
        }

        $response = $this->actingAs($user)->get(route('mylist.index'));

        // 2ページ目が存在する
        $response->assertSee('?page=2');
    }
}
