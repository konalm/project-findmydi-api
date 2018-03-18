--
-- PostgreSQL database dump
--

-- Dumped from database version 10.1
-- Dumped by pg_dump version 10.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: users; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA users;


ALTER SCHEMA users OWNER TO postgres;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: beta_signups; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE beta_signups (
    id integer NOT NULL,
    email character varying,
    signup_date date
);


ALTER TABLE beta_signups OWNER TO postgres;

--
-- Name: beta_signups_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE beta_signups_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE beta_signups_id_seq OWNER TO postgres;

--
-- Name: beta_signups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE beta_signups_id_seq OWNED BY beta_signups.id;


--
-- Name: instructor_adi_license_verifications; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE instructor_adi_license_verifications (
    user_id integer,
    status numeric,
    adi_license_src character varying,
    id integer NOT NULL,
    reject_reason text
);


ALTER TABLE instructor_adi_license_verifications OWNER TO postgres;

--
-- Name: instructor_adi_license_verifications_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE instructor_adi_license_verifications_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE instructor_adi_license_verifications_id_seq OWNER TO postgres;

--
-- Name: instructor_adi_license_verifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE instructor_adi_license_verifications_id_seq OWNED BY instructor_adi_license_verifications.id;


--
-- Name: instructor_coverage; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE instructor_coverage (
    id integer NOT NULL,
    user_id integer,
    postcode character varying,
    longitude character varying,
    latitude character varying,
    range integer,
    coverage_type character varying,
    region character varying
);


ALTER TABLE instructor_coverage OWNER TO postgres;

--
-- Name: instructor_coverage_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE instructor_coverage_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE instructor_coverage_id_seq OWNER TO postgres;

--
-- Name: instructor_coverage_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE instructor_coverage_id_seq OWNED BY instructor_coverage.id;


--
-- Name: instructor_inductions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE instructor_inductions (
    id integer NOT NULL,
    user_id numeric,
    intro_read boolean
);


ALTER TABLE instructor_inductions OWNER TO postgres;

--
-- Name: instructor_inductions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE instructor_inductions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE instructor_inductions_id_seq OWNER TO postgres;

--
-- Name: instructor_inductions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE instructor_inductions_id_seq OWNED BY instructor_inductions.id;


--
-- Name: instructors; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE instructors (
    id integer NOT NULL,
    first_name character varying,
    surname character varying,
    email character varying,
    adi_license_no character varying,
    gender character varying,
    verified boolean,
    hourly_rate numeric,
    offer text,
    password character varying,
    avatar_url character varying,
    contact_number numeric,
    adi_license_verified numeric,
    inducted boolean
);


ALTER TABLE instructors OWNER TO postgres;

--
-- Name: instructors_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE instructors_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE instructors_id_seq OWNER TO postgres;

--
-- Name: instructors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE instructors_id_seq OWNED BY instructors.id;


--
-- Name: review_invite_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE review_invite_tokens (
    id integer NOT NULL,
    instructor_id integer,
    name character varying,
    email character varying,
    token character varying,
    "timestamp" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    used boolean DEFAULT false
);


ALTER TABLE review_invite_tokens OWNER TO postgres;

--
-- Name: review_invite_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE review_invite_tokens_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE review_invite_tokens_id_seq OWNER TO postgres;

--
-- Name: review_invite_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE review_invite_tokens_id_seq OWNED BY review_invite_tokens.id;


--
-- Name: review_requests; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE review_requests (
    id integer NOT NULL,
    name character varying,
    email character varying,
    postcode character varying,
    instructor_name character varying,
    review_message text,
    rating double precision,
    "timestamp" timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE review_requests OWNER TO postgres;

--
-- Name: review_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE review_requests_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE review_requests_id_seq OWNER TO postgres;

--
-- Name: review_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE review_requests_id_seq OWNED BY review_requests.id;


--
-- Name: reviews; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE reviews (
    id integer NOT NULL,
    instructor_id integer,
    reviewer_name character varying,
    reviewer_email character varying,
    review_message text,
    rating double precision,
    "timestamp" timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE reviews OWNER TO postgres;

--
-- Name: reviews_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE reviews_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE reviews_id_seq OWNER TO postgres;

--
-- Name: reviews_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE reviews_id_seq OWNED BY reviews.id;


--
-- Name: super_admins; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE super_admins (
    id integer NOT NULL,
    username character varying,
    password character varying
);


ALTER TABLE super_admins OWNER TO postgres;

--
-- Name: super_admins_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE super_admins_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE super_admins_id_seq OWNER TO postgres;

--
-- Name: super_admins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE super_admins_id_seq OWNED BY super_admins.id;


--
-- Name: test; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE test (
    column_a name,
    column_b "char",
    column_c "char"
);


ALTER TABLE test OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE users (
    first_name character varying,
    email character varying,
    account_type integer,
    postcode character varying,
    range integer,
    distance_longitude character varying,
    distance_latitude character varying,
    password character varying,
    id integer NOT NULL,
    verified integer DEFAULT 0,
    has_avatar integer,
    surname character varying
);


ALTER TABLE users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


SET search_path = users, pg_catalog;

--
-- Name: users; Type: TABLE; Schema: users; Owner: postgres
--

CREATE TABLE users (
    name "char" NOT NULL,
    email "char" NOT NULL,
    postcode "char" NOT NULL
);


ALTER TABLE users OWNER TO postgres;

SET search_path = public, pg_catalog;

--
-- Name: beta_signups id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY beta_signups ALTER COLUMN id SET DEFAULT nextval('beta_signups_id_seq'::regclass);


--
-- Name: instructor_adi_license_verifications id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY instructor_adi_license_verifications ALTER COLUMN id SET DEFAULT nextval('instructor_adi_license_verifications_id_seq'::regclass);


--
-- Name: instructor_coverage id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY instructor_coverage ALTER COLUMN id SET DEFAULT nextval('instructor_coverage_id_seq'::regclass);


--
-- Name: instructor_inductions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY instructor_inductions ALTER COLUMN id SET DEFAULT nextval('instructor_inductions_id_seq'::regclass);


--
-- Name: instructors id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY instructors ALTER COLUMN id SET DEFAULT nextval('instructors_id_seq'::regclass);


--
-- Name: review_invite_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY review_invite_tokens ALTER COLUMN id SET DEFAULT nextval('review_invite_tokens_id_seq'::regclass);


--
-- Name: review_requests id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY review_requests ALTER COLUMN id SET DEFAULT nextval('review_requests_id_seq'::regclass);


--
-- Name: reviews id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY reviews ALTER COLUMN id SET DEFAULT nextval('reviews_id_seq'::regclass);


--
-- Name: super_admins id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY super_admins ALTER COLUMN id SET DEFAULT nextval('super_admins_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Data for Name: beta_signups; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY beta_signups (id, email, signup_date) FROM stdin;
1	connor@gmail.com	\N
2	connor@gmail.com	2018-03-06
3	lloyd@gmail.com	2018-03-06
4	lloyd@gmail.com	2018-03-06
5	benji@gmail.com	2018-03-06
6	frankie@gmail.com	2018-03-06
7	benji@gmail.com	2018-03-06
8	frankie100@gmail.com	2018-03-06
9	connor@codegood.co	2018-03-15
\.


--
-- Data for Name: instructor_adi_license_verifications; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY instructor_adi_license_verifications (user_id, status, adi_license_src, id, reject_reason) FROM stdin;
4	2	uploads/adiLicenceVerification/4.jpg	23	\N
19	1	uploads/adiLicenceVerification/19.jpg	24	\N
21	2	uploads/adiLicenceVerification/21.jpg	25	\N
\.


--
-- Data for Name: instructor_coverage; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY instructor_coverage (id, user_id, postcode, longitude, latitude, range, coverage_type, region) FROM stdin;
50	19	b187dw	-1.9361583356388	52.489225930163	3	postcode	\N
51	20	b187dw	-1.9361583356388	52.489225930163	3	postcode	\N
52	21	b187dw	-1.9361583356388	52.489225930163	3	postcode	\N
53	4	b187dw	-1.9361583356388	52.489225930163	3	postcode	\N
\.


--
-- Data for Name: instructor_inductions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY instructor_inductions (id, user_id, intro_read) FROM stdin;
1	4	t
2	18	f
3	19	t
4	20	t
5	21	t
\.


--
-- Data for Name: instructors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY instructors (id, first_name, surname, email, adi_license_no, gender, verified, hourly_rate, offer, password, avatar_url, contact_number, adi_license_verified, inducted) FROM stdin;
5	connor	moore	connor2@gmail.com	012345	male	f	\N	\N	$2y$10$evUJBv8rFQJFJlOwHoC7oOGqHlQwjCV/tnljKsbUQruINRoVCKyWO	\N	\N	\N	\N
6	connor	moore	connor2@gmail.com	012345	male	f	\N	\N	$2y$10$/zn58V58AZSKAPHvG2GniuvXHm4u77RITAmtE1LnXvXazqaHvB6JS	\N	\N	\N	\N
7	connor	moore	connor3@gmail.com	012345	male	f	\N	\N	$2y$10$KAkwbQ.g0.1fQEeJULDZaOq0f2ZIF2.FAYvv7NE2I3Dw0SNLrpW9G	\N	\N	\N	\N
8	connor	moore	connor3@gmail.com	012345	male	f	\N	\N	$2y$10$rp3G5mQwhj44aqeN11sYde5QQ9DO0aMvlhgtm3zQsU5GyyGCCfFDm	\N	\N	\N	\N
9	connor	moore	connor4@gmail.com	012345	male	f	\N	\N	$2y$10$1VfnHpZE9nhz8W8zBSBSjOtEle03THuXLZhOSK0xKRMZJbuZB0..G	\N	\N	\N	\N
10	connor	moore	connor4@gmail.com	012345	male	f	\N	\N	$2y$10$JipGpeEny004CCseYarTpunHVej1mcTeFkQIKgFtohvyVQYmVL1ry	\N	\N	\N	\N
11	connor	moore	connor5@gmail.com	012345	male	f	\N	\N	$2y$10$E63DkO3OBrkdstkx86X9nuh7mDqrvrmPQmT9RXLrkZXAOPYE5fxC6	\N	\N	\N	\N
12	connor	moore	connor6@gmail.com	012345	male	f	\N	\N	$2y$10$fj7W35.sW8yG2RXNWjQ1autVShkdQwhcg.7gF6ECnQAH.umD6IP56	\N	\N	\N	\N
15	lloyd	moore	lloyd@gmail.com	0121	male	f	\N	\N	$2y$10$0iU2D6Efd/G2s.D1bKN5FuEYozlyOv8STbNA.JF9K/53nV0OQskwe	\N	\N	\N	\N
16	connor	moore	connor7@gmail.com	012345	male	f	\N	\N	$2y$10$fpy1N.CSGlhz1hU/IqzpKuUyQgqvXXs//KYThylDxBXbbbjEP5ioi	\N	\N	\N	\N
17	connor	moore	connor8@gmail.com	012345	male	f	\N	\N	$2y$10$WOkcCx6ip84ckOo1kS7fFe4gdoLQuyu.YugXxLpld7y0R/JbsGIvS	\N	\N	\N	\N
18	connor	moore	connor9@gmail.com	012345	male	f	\N	\N	$2y$10$u7P1uPYhDNmzzWA4bBuhDuaeolzuHE.S4slMp4Mx9xpSxLrAofUVO	\N	\N	\N	\N
14	john	doe	johndoe@gmail.com	550493981	male	t	27	First 3 lessons only £11.00 for first time learners	$2y$10$IY6p6j1hsuNMOzPsw5qKJO0o7KHjulmdIxaFTdcPCcUreTxpw.qfa	uploads/instructorAvatar/14.jpg	121445667	\N	\N
19	lloyd	moore	lloyd230@gmail.com	013347	male	t	22	First 3 lessons £10.00	$2y$10$hbRD//FSzwzUnT0LTw17f.jzkoMrBtAzqzkm11AYoLSgbU0N0FsKG	uploads/instructorAvatar/19.jpg	\N	\N	t
20	connor	moore	connor_abo40@gmail.com	012359	male	f	20		$2y$10$vipmGmnOaxF/x1ayghJnNOe6vWeUrhSRALja0u5CJAFDC60WrXzB2	uploads/instructorAvatar/20.jpg	\N	\N	f
21	connor	moore	connor_fngj489@gmail.com	0127839	male	f	24		$2y$10$zqNNyVt6Z3kFhb2e3X8LduvinfJXH8T61ENHt92AOxadJ35NhDFoK	uploads/instructorAvatar/21.jpg	\N	\N	t
4	connor Lloyd	moore	connor@gmail.com	200000	male	t	22.00		$2y$10$B5SDXweH6QwOWRxkX1iPvO0bIj8jLoJuEkXDdqbFtkiUXA0zrT.Ke	uploads/instructorAvatar/4.jpg	121554112	\N	t
\.


--
-- Data for Name: review_invite_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY review_invite_tokens (id, instructor_id, name, email, token, "timestamp", used) FROM stdin;
29	4	connor lloyd	connor@codegood.co	HYaCtamuCDo9di1XzMkA9J	2018-03-12 23:05:27.966503	t
\.


--
-- Data for Name: review_requests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY review_requests (id, name, email, postcode, instructor_name, review_message, rating, "timestamp") FROM stdin;
1	lillyrose	lillyrose@gmail.com	b187dw	John Doe	John rock's 	5	2018-03-08 23:30:58.748794
2	lillyrose	lillyrose@gmail.com	b187dw	John Doe	\N	5	2018-03-08 23:31:37.780165
3	lillyrose	lillyrose@gmail.com	b187dw	John Doe	John rock's 	8	2018-03-08 23:31:50.047883
4	connor moore	connorstudent@gmail.com	b187dw	john doe	It was daunting 	4	2018-03-09 16:42:17.065771
\.


--
-- Data for Name: reviews; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY reviews (id, instructor_id, reviewer_name, reviewer_email, review_message, rating, "timestamp") FROM stdin;
14	4	connor lloyd	connor@codegood.co	Amazing instructors. Very clear and direct. Highly recommend	8.5	2018-03-12 23:06:26.000942
\.


--
-- Data for Name: super_admins; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY super_admins (id, username, password) FROM stdin;
1	creed_93	$2y$10$LRPMuWnPiJVHiy5vrn7WU.vyDNhGr.DRO54WevT6cyrgEjD7QqqUe
\.


--
-- Data for Name: test; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY test (column_a, column_b, column_c) FROM stdin;
t	t	t
value a	v	v
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY users (first_name, email, account_type, postcode, range, distance_longitude, distance_latitude, password, id, verified, has_avatar, surname) FROM stdin;
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	1	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	2	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	3	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	5	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	6	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	7	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	8	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	9	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	10	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	11	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	12	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	13	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	14	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	15	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	16	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	17	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	18	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	19	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	20	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	21	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	22	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	23	\N	\N	\N
\N	\N	\N	\N	\N	\N	\N	\N	24	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	25	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	\N	\N	\N	\N	26	\N	\N	\N
connor moore	connor@driver.com	2	b100	\N	\N	\N	\N	27	\N	\N	\N
connor lloyd	connor@driver.com	2	d100	\N	\N	\N	\N	28	\N	\N	\N
connor moore	connor@driving.com	2	d100	\N	\N	\N	\N	29	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	10	\N	\N	\N	30	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	10	\N	\N	\N	31	\N	\N	\N
connor lloyd	connor@driver.com	2	d100	5	\N	\N	\N	32	\N	\N	\N
Mr X	x@gmail.com	2	b12 x11	10	\N	\N	\N	33	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	34	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	35	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	36	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	37	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	38	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	39	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	40	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	41	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	42	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	43	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	44	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	45	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	46	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	\N	\N	\N	47	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	-1.9361583356388	52.489225930163	\N	48	\N	\N	\N
Mr X	x@gmail.com	2	b322qx	10	-1.9823414184904	52.45534063198	\N	49	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	-1.9361583356388	52.489225930163	\N	50	\N	\N	\N
Mr X	x@gmail.com	2	b322qx	10	-1.98234	52.45534	\N	51	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	-1.93616	52.48923	\N	52	\N	\N	\N
Mr X	x@gmail.com	2	b187dw	10	-1.93616	52.48923	\N	53	\N	\N	\N
Mr X	x@gmail.com	2	B193AB	3	-1.90329	52.48653	\N	54	\N	\N	\N
Mr X	x@gmail.com	2	B168HQ	3	-1.92253	52.47473	\N	55	\N	\N	\N
Mr laydwood	lady@gmail.com	2	B168HQ	3	-1.92253	52.47473	\N	56	\N	\N	\N
lloyd moore	lloydmoore@gmail.com	2	NE21TN	4	-1.60069	54.98476	\N	57	\N	\N	\N
lloyd moore	lloydmoore@gmail.com	2	L40TH	4	-2.96158	53.43127	\N	58	\N	\N	\N
homer simpson	homer@gmail.com	2	\N	\N	\N	\N	$2y$10$h82oJmm0FUFboDuXwqFNme9bH8J7qZIy2GN/IdjUyXmTAMJkVZOWu	59	\N	\N	\N
CONNOR MOORE	connor@codegood.co	222	\N	\N	\N	\N	$2y$10$AWCScp5LJ3r2psZmTxQCluC10tZHV.428.wl1SwdBLAqVmothjol6	61	\N	\N	\N
MR I	i@gmail.com	2	\N	\N	\N	\N	$2y$10$17IC/OAQzbyCN9mcpUvhmOj7e67WgewEjSapXmRV7PnGn7jhCigCG	63	2	\N	\N
homer simpson	homer2@gmail.com	2	b187dw	7	-1.9361583356388	52.489225930163	$2y$10$4La6d0Coph6nrRHCEUS9lehN3UP3u5J.1vt5hI8U4KiaFK.ua4ZSC	60	1	1	\N
Mr X	x@gmail.com	2	b187dw	6	-1.9361583356388	52.489225930163	\N	4	\N	1	\N
homer simpson	homer22@gmail.com	2	\N	\N	\N	\N	$2y$10$Hj1oTKGO3WSm8bhjVhJkSuWMw6XTH/XiqeIwgge/xhJMB0grg8ulO	62	0	\N	\N
\.


SET search_path = users, pg_catalog;

--
-- Data for Name: users; Type: TABLE DATA; Schema: users; Owner: postgres
--

COPY users (name, email, postcode) FROM stdin;
\.


SET search_path = public, pg_catalog;

--
-- Name: beta_signups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('beta_signups_id_seq', 9, true);


--
-- Name: instructor_adi_license_verifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('instructor_adi_license_verifications_id_seq', 25, true);


--
-- Name: instructor_coverage_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('instructor_coverage_id_seq', 53, true);


--
-- Name: instructor_inductions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('instructor_inductions_id_seq', 5, true);


--
-- Name: instructors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('instructors_id_seq', 21, true);


--
-- Name: review_invite_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('review_invite_tokens_id_seq', 29, true);


--
-- Name: review_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('review_requests_id_seq', 4, true);


--
-- Name: reviews_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('reviews_id_seq', 14, true);


--
-- Name: super_admins_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('super_admins_id_seq', 1, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('users_id_seq', 63, true);


--
-- Name: instructor_adi_license_verifications instructor_adi_license_verifications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY instructor_adi_license_verifications
    ADD CONSTRAINT instructor_adi_license_verifications_pkey PRIMARY KEY (id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--

