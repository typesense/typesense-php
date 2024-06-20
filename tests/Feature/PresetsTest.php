<?php

namespace Feature;

use Tests\TestCase;

class PresetsTest extends TestCase
{
    private $presetUpsertRes = null;
    private $presetName = 'test-preset';


    protected function setUp(): void
    {
        parent::setUp();

        $returnData =  $this->client()->presets->put([
            'preset_name' => $this->presetName,
            'preset_data' =>  [
                'value' => [
                    'query_by' => "*",
                ],
            ]
        ]);
        $this->presetUpsertRes = $returnData;
    }

    protected function tearDown(): void
    {
        $presets = $this->client()->presets->get();
        foreach ($presets['presets'] as $preset) {
            $this->client()->presets->delete($preset['name']);
        }
    }

    public function testCanUpsertAPreset(): void
    {
        $this->assertEquals($this->presetName, $this->presetUpsertRes['name']);
    }

    //* Currently there isn't a method for retrieving a preset by name
    // public function testCanRetrieveAPreset(): void
    // {
    //     $returnData =  $this->client()->presets->get($this->presetName);
    //     $this->assertEquals($this->presetName, $returnData['name']);
    // }

    public function testCanDeleteAPreset(): void
    {
        $returnData =  $this->client()->presets->delete($this->presetName);
        $this->assertEquals($this->presetName, $returnData['name']);

        $returnPresets =  $this->client()->presets->get();
        $this->assertCount(0, $returnPresets['presets']);
    }

    public function testCanRetrieveAllPresets(): void
    {
        $returnData =  $this->client()->presets->get();
        $this->assertCount(1, $returnData['presets']);
    }
}
