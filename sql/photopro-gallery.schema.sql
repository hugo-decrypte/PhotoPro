DROP TABLE IF EXISTS "users";

CREATE TYPE type as ENUM ('private', 'public');

CREATE TABLE "public"."gallery" (
                                "id" uuid NOT NULL,
                                "photographerId" uuid NOT NULL,
                                "title" character varying(128) NOT NULL,
                                "description" character varying(256) NOT NULL,
                                "status" smallint DEFAULT '0' NOT NULL,
                                "type" type DEFAULT 'private',
                                "coverPhotoId" uuid NOT NULL,
                                CONSTRAINT "users_id" PRIMARY KEY ("id")
) WITH (oids = false);