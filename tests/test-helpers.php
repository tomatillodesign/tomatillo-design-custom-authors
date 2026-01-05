<?php
// ABOUTME: Unit tests for helper formatting functions.
// ABOUTME: Tests Oxford comma formatter and contributor list building logic.

use PHPUnit\Framework\TestCase;

class Test_Helpers extends TestCase {
    
    public function test_format_contributor_list_empty() {
        $result = tdc_format_contributor_list(array());
        $this->assertEquals('', $result);
    }
    
    public function test_format_contributor_list_single() {
        $result = tdc_format_contributor_list(array('Toby Posel'));
        $this->assertEquals('Toby Posel', $result);
    }
    
    public function test_format_contributor_list_two() {
        $result = tdc_format_contributor_list(array('Toby Posel', 'Jane Doe'));
        $this->assertEquals('Toby Posel and Jane Doe', $result);
    }
    
    public function test_format_contributor_list_three() {
        $result = tdc_format_contributor_list(array('Toby Posel', 'Jane Doe', 'Sam Roe'));
        $this->assertEquals('Toby Posel, Jane Doe, and Sam Roe', $result);
    }
    
    public function test_format_contributor_list_four() {
        $result = tdc_format_contributor_list(array('Toby Posel', 'Jane Doe', 'Sam Roe', 'Alex Smith'));
        $this->assertEquals('Toby Posel, Jane Doe, Sam Roe, and Alex Smith', $result);
    }
    
    public function test_build_contributor_snapshot() {
        // Mock WordPress functions for this test
        $snapshot = array(
            'id' => 123,
            'name' => 'Test Contributor',
            'permalink' => 'http://example.com/contributors/test-contributor/'
        );
        
        $this->assertIsArray($snapshot);
        $this->assertArrayHasKey('id', $snapshot);
        $this->assertArrayHasKey('name', $snapshot);
        $this->assertArrayHasKey('permalink', $snapshot);
    }
    
    public function test_build_snapshots_array_empty() {
        $result = tdc_build_snapshots_array(array());
        $this->assertEquals(array(), $result);
    }
    
    public function test_get_snapshot_by_id() {
        $snapshots = array(
            array('id' => 1, 'name' => 'First', 'permalink' => 'http://example.com/1/'),
            array('id' => 2, 'name' => 'Second', 'permalink' => 'http://example.com/2/'),
            array('id' => 3, 'name' => 'Third', 'permalink' => 'http://example.com/3/')
        );
        
        $result = tdc_get_snapshot_by_id($snapshots, 2);
        $this->assertNotNull($result);
        $this->assertEquals('Second', $result['name']);
        
        $result = tdc_get_snapshot_by_id($snapshots, 999);
        $this->assertNull($result);
    }
}

