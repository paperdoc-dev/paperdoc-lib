<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Document;

use PHPUnit\Framework\TestCase;
use Paperdoc\Document\Metadata;

class MetadataTest extends TestCase
{
    public function test_defaults_are_empty(): void
    {
        $m = new Metadata();

        $this->assertSame('', $m->getAuthor());
        $this->assertSame('', $m->getSubject());
        $this->assertSame('', $m->getDescription());
        $this->assertSame('', $m->getKeywords());
        $this->assertSame('', $m->getLanguage());
        $this->assertNull($m->getCreatedAt());
        $this->assertNull($m->getModifiedAt());
    }

    public function test_make_factory(): void
    {
        $this->assertInstanceOf(Metadata::class, Metadata::make());
    }

    public function test_fluent_setters(): void
    {
        $m = Metadata::make()
            ->setAuthor('Alice')
            ->setSubject('Report')
            ->setDescription('An important document')
            ->setKeywords('finance, q1, 2026')
            ->setLanguage('en-US');

        $this->assertSame('Alice', $m->getAuthor());
        $this->assertSame('Report', $m->getSubject());
        $this->assertSame('An important document', $m->getDescription());
        $this->assertSame('finance, q1, 2026', $m->getKeywords());
        $this->assertSame('en-US', $m->getLanguage());
    }

    public function test_set_created_at_from_datetime(): void
    {
        $dt = new \DateTimeImmutable('2026-04-24T20:00:00+00:00');
        $m  = Metadata::make()->setCreatedAt($dt);

        $this->assertSame($dt, $m->getCreatedAt());
    }

    public function test_set_created_at_from_mutable_datetime(): void
    {
        $mutable = new \DateTime('2026-04-24T20:00:00+00:00');
        $m       = Metadata::make()->setCreatedAt($mutable);

        $this->assertInstanceOf(\DateTimeImmutable::class, $m->getCreatedAt());
        $this->assertSame('2026-04-24T20:00:00+00:00', $m->getCreatedAt()->format(\DateTimeInterface::ATOM));
    }

    public function test_set_created_at_null_clears(): void
    {
        $m = Metadata::make()
            ->setCreatedAt(new \DateTimeImmutable())
            ->setCreatedAt(null);

        $this->assertNull($m->getCreatedAt());
    }

    public function test_from_array_full(): void
    {
        $m = Metadata::fromArray([
            'author'      => 'Bob',
            'subject'     => 'Subj',
            'description' => 'Desc',
            'keywords'    => 'a, b',
            'createdAt'   => '2026-01-01T00:00:00+00:00',
            'modifiedAt'  => '2026-04-01T10:00:00+00:00',
            'language'    => 'fr-FR',
        ]);

        $this->assertSame('Bob', $m->getAuthor());
        $this->assertSame('fr-FR', $m->getLanguage());
        $this->assertInstanceOf(\DateTimeImmutable::class, $m->getCreatedAt());
        $this->assertSame('2026-01-01T00:00:00+00:00', $m->getCreatedAt()->format(\DateTimeInterface::ATOM));
        $this->assertSame('2026-04-01T10:00:00+00:00', $m->getModifiedAt()->format(\DateTimeInterface::ATOM));
    }

    public function test_from_array_partial(): void
    {
        $m = Metadata::fromArray(['author' => 'Alice']);

        $this->assertSame('Alice', $m->getAuthor());
        $this->assertSame('', $m->getSubject());
        $this->assertNull($m->getCreatedAt());
    }

    public function test_to_array_round_trip(): void
    {
        $created  = new \DateTimeImmutable('2026-01-01T12:00:00+00:00');
        $modified = new \DateTimeImmutable('2026-04-24T08:00:00+00:00');

        $m = Metadata::make()
            ->setAuthor('Alice')
            ->setSubject('S')
            ->setCreatedAt($created)
            ->setModifiedAt($modified)
            ->setLanguage('en');

        $array = $m->toArray();

        $this->assertSame('Alice', $array['author']);
        $this->assertSame('2026-01-01T12:00:00+00:00', $array['createdAt']);
        $this->assertSame('2026-04-24T08:00:00+00:00', $array['modifiedAt']);

        $round = Metadata::fromArray($array);

        $this->assertEquals($m->getAuthor(),     $round->getAuthor());
        $this->assertEquals($m->getCreatedAt(),  $round->getCreatedAt());
        $this->assertEquals($m->getModifiedAt(), $round->getModifiedAt());
    }

    public function test_json_serialize_omits_empty_values(): void
    {
        $m = Metadata::make()->setAuthor('Alice');

        $json = json_decode(json_encode($m, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['author' => 'Alice'], $json);
    }

    public function test_json_serialize_includes_dates(): void
    {
        $m = Metadata::make()
            ->setAuthor('Alice')
            ->setCreatedAt(new \DateTimeImmutable('2026-01-01T00:00:00+00:00'));

        $json = json_decode(json_encode($m, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('Alice', $json['author']);
        $this->assertSame('2026-01-01T00:00:00+00:00', $json['createdAt']);
        $this->assertArrayNotHasKey('subject', $json);
    }
}
