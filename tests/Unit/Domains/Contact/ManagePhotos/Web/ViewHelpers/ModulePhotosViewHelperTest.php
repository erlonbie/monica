<?php

namespace Tests\Unit\Domains\Contact\ManagePhotos\Web\ViewHelpers;

use App\Domains\Contact\ManagePhotos\Web\ViewHelpers\ModulePhotosViewHelper;
use App\Models\Contact;
use App\Models\File;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModulePhotosViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contact = Contact::factory()->create();
        $file = File::factory()->create([
            'vault_id' => $contact->vault_id,
        ]);
        $contact->files()->save($file);

        $array = ModulePhotosViewHelper::data($contact);

        $this->assertEquals(
            3,
            count($array)
        );

        $this->assertArrayHasKey('photos', $array);
        $this->assertArrayHasKey('canUploadFile', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertFalse($array['canUploadFile']);
        $this->assertEquals(
            [
                'index' => route('contact.photo.index', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'store' => route('contact.photo.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_transfer_object(): void
    {
        $contact = Contact::factory()->create();
        $file = File::factory()->create([
            'vault_id' => $contact->vault_id,
            'size' => 123,
            'uuid' => 123,
        ]);

        $array = ModulePhotosViewHelper::dto($file, $contact);

        $this->assertEquals(
            [
                'id' => $file->id,
                'name' => $file->name,
                'mime_type' => $file->mime_type,
                'size' => '123B',
                'url' => [
                    'display' => $file->cdn_url,
                    'download' => $file->cdn_url,
                    'show' => route('contact.photo.show', [
                        'vault' => $contact->vault_id,
                        'contact' => $contact->id,
                        'photo' => $file->id,
                    ]),
                    'destroy' => route('contact.photo.destroy', [
                        'vault' => $contact->vault_id,
                        'contact' => $contact->id,
                        'photo' => $file->id,
                    ]),
                ],
            ],
            $array
        );
    }
}
