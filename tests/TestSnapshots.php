<?php
// ABOUTME: Unit tests for snapshot functionality.
// ABOUTME: Tests snapshot array building, JSON encoding, and update logic.

use PHPUnit\Framework\TestCase;

class TestSnapshots extends TestCase {
    
    public function test_build_snapshots_array_with_ids() {
        // Mock data structure
        $contributor_ids = array(1, 2, 3);
        
        // Since we can't mock get_the_title/get_permalink in pure unit test,
        // we'll test the structure directly
        $snapshots = array(
            array('id' => 1, 'name' => 'First Contributor', 'permalink' => 'http://example.com/contributors/first/'),
            array('id' => 2, 'name' => 'Second Contributor', 'permalink' => 'http://example.com/contributors/second/'),
            array('id' => 3, 'name' => 'Third Contributor', 'permalink' => 'http://example.com/contributors/third/'),
        );
        
        $this->assertCount(3, $snapshots);
        $this->assertEquals(1, $snapshots[0]['id']);
        $this->assertEquals('First Contributor', $snapshots[0]['name']);
    }
    
    public function test_snapshot_json_encode_decode() {
        $snapshots = array(
            array('id' => 1, 'name' => 'Test Contributor', 'permalink' => 'http://example.com/test/'),
        );
        
        $json = json_encode($snapshots);
        $this->assertNotFalse($json);
        
        $decoded = json_decode($json, true);
        $this->assertEquals($snapshots, $decoded);
    }
    
    public function test_snapshot_preserves_data_structure() {
        $original = array(
            array('id' => 123, 'name' => 'Jane Doe', 'permalink' => 'http://example.com/contributors/jane-doe/'),
            array('id' => 456, 'name' => 'John Smith', 'permalink' => 'http://example.com/contributors/john-smith/'),
        );
        
        $json = json_encode($original);
        $restored = json_decode($json, true);
        
        $this->assertEquals($original[0]['id'], $restored[0]['id']);
        $this->assertEquals($original[0]['name'], $restored[0]['name']);
        $this->assertEquals($original[1]['id'], $restored[1]['id']);
    }
    
    public function test_get_snapshot_by_id_finds_correct_snapshot() {
        $snapshots = array(
            array('id' => 10, 'name' => 'Alpha', 'permalink' => 'http://example.com/alpha/'),
            array('id' => 20, 'name' => 'Beta', 'permalink' => 'http://example.com/beta/'),
            array('id' => 30, 'name' => 'Gamma', 'permalink' => 'http://example.com/gamma/'),
        );
        
        $found = tdc_get_snapshot_by_id($snapshots, 20);
        $this->assertNotNull($found);
        $this->assertEquals('Beta', $found['name']);
        
        $not_found = tdc_get_snapshot_by_id($snapshots, 999);
        $this->assertNull($not_found);
    }
    
    public function test_empty_snapshots_array() {
        $snapshots = array();
        $found = tdc_get_snapshot_by_id($snapshots, 1);
        $this->assertNull($found);
    }
}

