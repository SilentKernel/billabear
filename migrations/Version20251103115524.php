<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Customer Registration Link Migration
 */
final class Version20251103115524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add customer_registrations table for public customer registration links';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE customer_registrations (id UUID NOT NULL, brand_settings_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, created_by_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, permanent BOOLEAN NOT NULL, valid BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CUSTOMER_REG_SLUG ON customer_registrations (slug)');
        $this->addSql('CREATE INDEX IDX_CUSTOMER_REG_BRAND ON customer_registrations (brand_settings_id)');
        $this->addSql('CREATE INDEX IDX_CUSTOMER_REG_CUSTOMER ON customer_registrations (customer_id)');
        $this->addSql('CREATE INDEX IDX_CUSTOMER_REG_CREATED_BY ON customer_registrations (created_by_id)');
        $this->addSql('COMMENT ON COLUMN customer_registrations.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_registrations.brand_settings_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_registrations.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_registrations.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE customer_registrations ADD CONSTRAINT FK_CUSTOMER_REG_BRAND_FK FOREIGN KEY (brand_settings_id) REFERENCES brand_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_registrations ADD CONSTRAINT FK_CUSTOMER_REG_CUSTOMER_FK FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_registrations ADD CONSTRAINT FK_CUSTOMER_REG_CREATED_BY_FK FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer_registrations DROP CONSTRAINT FK_CUSTOMER_REG_BRAND_FK');
        $this->addSql('ALTER TABLE customer_registrations DROP CONSTRAINT FK_CUSTOMER_REG_CUSTOMER_FK');
        $this->addSql('ALTER TABLE customer_registrations DROP CONSTRAINT FK_CUSTOMER_REG_CREATED_BY_FK');
        $this->addSql('DROP TABLE customer_registrations');
    }
}
