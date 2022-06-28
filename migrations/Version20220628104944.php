<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628104944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE destination (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_tour (service_id INT NOT NULL, tour_id INT NOT NULL, INDEX IDX_A4924D85ED5CA9E6 (service_id), INDEX IDX_A4924D8515ED8D43 (tour_id), PRIMARY KEY(service_id, tour_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tour (id INT AUTO_INCREMENT NOT NULL, created_user_id INT NOT NULL, title VARCHAR(255) NOT NULL, duration INT NOT NULL, max_people INT NOT NULL, min_age INT NOT NULL, over_view VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', price DOUBLE PRECISION NOT NULL, INDEX IDX_6AD1F969E104C1D3 (created_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tour_image (id INT AUTO_INCREMENT NOT NULL, tour_id INT NOT NULL, image_id INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_FE72147715ED8D43 (tour_id), UNIQUE INDEX UNIQ_FE7214773DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tour_plan (id INT AUTO_INCREMENT NOT NULL, tour_id INT NOT NULL, destination_id INT NOT NULL, day INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FC6ABB5D15ED8D43 (tour_id), INDEX IDX_FC6ABB5D816C6140 (destination_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, image_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6493DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_tour ADD CONSTRAINT FK_A4924D85ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_tour ADD CONSTRAINT FK_A4924D8515ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tour ADD CONSTRAINT FK_6AD1F969E104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tour_image ADD CONSTRAINT FK_FE72147715ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id)');
        $this->addSql('ALTER TABLE tour_image ADD CONSTRAINT FK_FE7214773DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE tour_plan ADD CONSTRAINT FK_FC6ABB5D15ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id)');
        $this->addSql('ALTER TABLE tour_plan ADD CONSTRAINT FK_FC6ABB5D816C6140 FOREIGN KEY (destination_id) REFERENCES destination (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tour_plan DROP FOREIGN KEY FK_FC6ABB5D816C6140');
        $this->addSql('ALTER TABLE tour_image DROP FOREIGN KEY FK_FE7214773DA5256D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493DA5256D');
        $this->addSql('ALTER TABLE service_tour DROP FOREIGN KEY FK_A4924D85ED5CA9E6');
        $this->addSql('ALTER TABLE service_tour DROP FOREIGN KEY FK_A4924D8515ED8D43');
        $this->addSql('ALTER TABLE tour_image DROP FOREIGN KEY FK_FE72147715ED8D43');
        $this->addSql('ALTER TABLE tour_plan DROP FOREIGN KEY FK_FC6ABB5D15ED8D43');
        $this->addSql('ALTER TABLE tour DROP FOREIGN KEY FK_6AD1F969E104C1D3');
        $this->addSql('DROP TABLE destination');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_tour');
        $this->addSql('DROP TABLE tour');
        $this->addSql('DROP TABLE tour_image');
        $this->addSql('DROP TABLE tour_plan');
        $this->addSql('DROP TABLE user');
    }
}
