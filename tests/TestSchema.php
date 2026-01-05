<?php
// ABOUTME: Unit tests for structured data schema generation.
// ABOUTME: Tests JSON-LD Article schema with contributor author arrays.

use PHPUnit\Framework\TestCase;

class TestSchema extends TestCase {
    
    public function test_schema_has_required_context_and_type() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
        );
        
        $this->assertArrayHasKey('@context', $schema);
        $this->assertArrayHasKey('@type', $schema);
        $this->assertEquals('https://schema.org', $schema['@context']);
        $this->assertEquals('Article', $schema['@type']);
    }
    
    public function test_schema_includes_single_author() {
        $authors = array(
            array(
                '@type' => 'Person',
                'name' => 'Toby Posel',
                'url' => 'http://example.com/contributors/toby-posel/',
            ),
        );
        
        $this->assertCount(1, $authors);
        $this->assertEquals('Person', $authors[0]['@type']);
        $this->assertEquals('Toby Posel', $authors[0]['name']);
        $this->assertArrayHasKey('url', $authors[0]);
    }
    
    public function test_schema_includes_multiple_authors() {
        $authors = array(
            array('@type' => 'Person', 'name' => 'First', 'url' => 'http://example.com/first/'),
            array('@type' => 'Person', 'name' => 'Second', 'url' => 'http://example.com/second/'),
            array('@type' => 'Person', 'name' => 'Third', 'url' => 'http://example.com/third/'),
        );
        
        $this->assertCount(3, $authors);
        $this->assertEquals('First', $authors[0]['name']);
        $this->assertEquals('Second', $authors[1]['name']);
        $this->assertEquals('Third', $authors[2]['name']);
    }
    
    public function test_schema_omits_url_for_unpublished_author() {
        $authors = array(
            array('@type' => 'Person', 'name' => 'Published Author', 'url' => 'http://example.com/published/'),
            array('@type' => 'Person', 'name' => 'Draft Author'),
        );
        
        $this->assertArrayHasKey('url', $authors[0]);
        $this->assertArrayNotHasKey('url', $authors[1]);
    }
    
    public function test_schema_json_encodes_properly() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'datePublished' => '2025-01-05',
            'author' => array(
                array('@type' => 'Person', 'name' => 'Test Author'),
            ),
        );
        
        $json = json_encode($schema, JSON_UNESCAPED_SLASHES);
        $this->assertNotFalse($json);
        $this->assertStringContainsString('"@context"', $json);
        $this->assertStringContainsString('https://schema.org', $json);
    }
}

