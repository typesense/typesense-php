<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;

class PresetsTest extends TestCase
{
    private $presetUpsertRes = null;
    private $presetName = 'test-preset';


    protected function setUp(): void
    {
        parent::setUp();

        $returnData =  $this->client()->presets->upsert($this->presetName, [
            'value' => [
                'query_by' => "*",
            ],
        ]);
        $this->presetUpsertRes = $returnData;
    }

    protected function tearDown(): void
    {
        $presets = $this->client()->presets->retrieve();
        foreach ($presets['presets'] as $preset) {
            $this->client()->presets[$preset['name']]->delete();
        }
    }

    public function testCanUpsertAPreset(): void
    {
        $this->assertEquals($this->presetName, $this->presetUpsertRes['name']);
    }

    public function testCanRetrieveAPresetByName(): void
    {
        $returnData =  $this->client()->presets[$this->presetName]->retrieve();
        $this->assertEquals($this->presetName, $returnData['name']);
    }

    public function testCanDeleteAPreset(): void
    {
        $returnData =  $this->client()->presets[$this->presetName]->delete();
        $this->assertEquals($this->presetName, $returnData['name']);

        $this->expectException(ObjectNotFound::class);
        $this->client()->presets[$this->presetName]->retrieve();
    }

    public function testCanRetrieveAllPresets(): void
    {
        $returnData =  $this->client()->presets->retrieve();
        $this->assertCount(1, $returnData['presets']);
    }
}
