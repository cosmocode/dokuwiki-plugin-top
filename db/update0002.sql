CREATE TEMPORARY TABLE toptemp (
    page,
    month,
    lang,
    value,
    PRIMARY KEY(page)
);

INSERT INTO toptemp (page, month, lang, value) SELECT page, '', '', value FROM toppages;

DROP TABLE toppages;

CREATE TABLE toppages (
    page,
    month,
    lang,
    value,
    PRIMARY KEY(page)
);

INSERT INTO toppages (page, month, lang, value) SELECT page, month, lang, value FROM toptemp;

DROP TABLE toptemp;
