--
-- PostgreSQL database dump
--

\restrict EML2HwCFcEUgyaU9QD8h4HoCAUAaFRbJrcikmF7jTsLDeOWUv0DXUn6Pkl40lu2

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: audit_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.audit_logs (
    id bigint NOT NULL,
    user_id bigint,
    action character varying(255) NOT NULL,
    model_type character varying(255),
    model_id bigint,
    old_values json,
    new_values json,
    extra json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.audit_logs OWNER TO postgres;

--
-- Name: audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.audit_logs_id_seq OWNER TO postgres;

--
-- Name: audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.audit_logs_id_seq OWNED BY public.audit_logs.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: consumos_materia_prima; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.consumos_materia_prima (
    id bigint NOT NULL,
    produccion_id bigint NOT NULL,
    materia_prima_id bigint NOT NULL,
    cantidad_consumida numeric(12,4) NOT NULL,
    costo_unitario_momento numeric(12,4) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.consumos_materia_prima OWNER TO postgres;

--
-- Name: consumos_materia_prima_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.consumos_materia_prima_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.consumos_materia_prima_id_seq OWNER TO postgres;

--
-- Name: consumos_materia_prima_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.consumos_materia_prima_id_seq OWNED BY public.consumos_materia_prima.id;


--
-- Name: controles_calidad; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.controles_calidad (
    id bigint NOT NULL,
    produccion_id bigint NOT NULL,
    producto_id bigint NOT NULL,
    fecha_inspeccion date NOT NULL,
    resultado character varying(255) NOT NULL,
    motivo_rechazo character varying(255),
    observaciones text,
    inspector_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT controles_calidad_resultado_check CHECK (((resultado)::text = ANY ((ARRAY['aprobado'::character varying, 'rechazado'::character varying, 'cuarentena'::character varying])::text[])))
);


ALTER TABLE public.controles_calidad OWNER TO postgres;

--
-- Name: controles_calidad_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.controles_calidad_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.controles_calidad_id_seq OWNER TO postgres;

--
-- Name: controles_calidad_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.controles_calidad_id_seq OWNED BY public.controles_calidad.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: materia_prima; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.materia_prima (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    sku character varying(255) NOT NULL,
    unidad character varying(255) DEFAULT 'kg'::character varying NOT NULL,
    stock_actual numeric(12,4) DEFAULT '0'::numeric NOT NULL,
    stock_minimo numeric(12,4) DEFAULT '0'::numeric NOT NULL,
    costo_unitario numeric(12,4),
    proveedor character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    lote_compra character varying(255),
    fecha_vencimiento date
);


ALTER TABLE public.materia_prima OWNER TO postgres;

--
-- Name: materia_prima_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.materia_prima_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.materia_prima_id_seq OWNER TO postgres;

--
-- Name: materia_prima_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.materia_prima_id_seq OWNED BY public.materia_prima.id;


--
-- Name: mermas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mermas (
    id bigint NOT NULL,
    producto_id bigint NOT NULL,
    cantidad numeric(10,2) NOT NULL,
    unidad character varying(255) DEFAULT 'kg'::character varying NOT NULL,
    causa character varying(255) NOT NULL,
    tipo_merma character varying(255) NOT NULL,
    lote character varying(255),
    fecha date NOT NULL,
    usuario_id bigint NOT NULL,
    observaciones text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    produccion_id bigint,
    CONSTRAINT mermas_tipo_merma_check CHECK (((tipo_merma)::text = ANY ((ARRAY['produccion'::character varying, 'empaquetado'::character varying])::text[])))
);


ALTER TABLE public.mermas OWNER TO postgres;

--
-- Name: mermas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mermas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mermas_id_seq OWNER TO postgres;

--
-- Name: mermas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mermas_id_seq OWNED BY public.mermas.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: movimientos_materia_prima; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.movimientos_materia_prima (
    id bigint NOT NULL,
    materia_prima_id bigint NOT NULL,
    tipo character varying(255) NOT NULL,
    cantidad numeric(12,4) NOT NULL,
    motivo character varying(255) NOT NULL,
    referencia_tipo character varying(255),
    referencia_id bigint,
    costo_unitario_momento numeric(12,4),
    observaciones text,
    usuario_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    fecha_movimiento timestamp(0) without time zone,
    CONSTRAINT movimientos_materia_prima_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['entrada'::character varying, 'salida'::character varying])::text[])))
);


ALTER TABLE public.movimientos_materia_prima OWNER TO postgres;

--
-- Name: movimientos_materia_prima_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.movimientos_materia_prima_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.movimientos_materia_prima_id_seq OWNER TO postgres;

--
-- Name: movimientos_materia_prima_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.movimientos_materia_prima_id_seq OWNED BY public.movimientos_materia_prima.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: producciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.producciones (
    id bigint NOT NULL,
    lote character varying(255),
    producto_id bigint NOT NULL,
    cantidad_producida numeric(12,4) NOT NULL,
    fecha_produccion date NOT NULL,
    observaciones text,
    usuario_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    materia_prima_desechada numeric(12,4) DEFAULT '0'::numeric NOT NULL,
    producto_desechado numeric(12,4) DEFAULT '0'::numeric NOT NULL,
    calidad_observaciones text,
    eficiencia numeric(5,2)
);


ALTER TABLE public.producciones OWNER TO postgres;

--
-- Name: producciones_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.producciones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.producciones_id_seq OWNER TO postgres;

--
-- Name: producciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.producciones_id_seq OWNED BY public.producciones.id;


--
-- Name: products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.products (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    sku character varying(255) NOT NULL,
    categoria character varying(255) NOT NULL,
    stock_actual numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    unidad character varying(255) DEFAULT 'kg'::character varying NOT NULL,
    precio_unitario numeric(10,2),
    stock_minimo numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.products OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.products_id_seq OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- Name: recetas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.recetas (
    id bigint NOT NULL,
    producto_id bigint NOT NULL,
    materia_prima_id bigint NOT NULL,
    cantidad_necesaria numeric(12,4) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.recetas OWNER TO postgres;

--
-- Name: recetas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.recetas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.recetas_id_seq OWNER TO postgres;

--
-- Name: recetas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.recetas_id_seq OWNED BY public.recetas.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    role character varying(255) DEFAULT 'operador'::character varying NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    requested_role character varying(255),
    approved_by bigint,
    approved_at timestamp(0) without time zone,
    rejection_reason text,
    CONSTRAINT users_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'active'::character varying, 'suspended'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: audit_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs ALTER COLUMN id SET DEFAULT nextval('public.audit_logs_id_seq'::regclass);


--
-- Name: consumos_materia_prima id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consumos_materia_prima ALTER COLUMN id SET DEFAULT nextval('public.consumos_materia_prima_id_seq'::regclass);


--
-- Name: controles_calidad id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.controles_calidad ALTER COLUMN id SET DEFAULT nextval('public.controles_calidad_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: materia_prima id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.materia_prima ALTER COLUMN id SET DEFAULT nextval('public.materia_prima_id_seq'::regclass);


--
-- Name: mermas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mermas ALTER COLUMN id SET DEFAULT nextval('public.mermas_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: movimientos_materia_prima id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.movimientos_materia_prima ALTER COLUMN id SET DEFAULT nextval('public.movimientos_materia_prima_id_seq'::regclass);


--
-- Name: producciones id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producciones ALTER COLUMN id SET DEFAULT nextval('public.producciones_id_seq'::regclass);


--
-- Name: products id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- Name: recetas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas ALTER COLUMN id SET DEFAULT nextval('public.recetas_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: audit_logs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.audit_logs (id, user_id, action, model_type, model_id, old_values, new_values, extra, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: consumos_materia_prima; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.consumos_materia_prima (id, produccion_id, materia_prima_id, cantidad_consumida, costo_unitario_momento, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: controles_calidad; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.controles_calidad (id, produccion_id, producto_id, fecha_inspeccion, resultado, motivo_rechazo, observaciones, inspector_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: materia_prima; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.materia_prima (id, nombre, sku, unidad, stock_actual, stock_minimo, costo_unitario, proveedor, created_at, updated_at, deleted_at, lote_compra, fecha_vencimiento) FROM stdin;
\.


--
-- Data for Name: mermas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.mermas (id, producto_id, cantidad, unidad, causa, tipo_merma, lote, fecha, usuario_id, observaciones, created_at, updated_at, produccion_id) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_05_26_153522_create_products_table	1
5	2026_05_29_194236_create_mermas_table	1
6	2026_05_29_210747_add_code_to_products_table	1
7	2026_05_29_211151_drop_code_from_products_table	1
8	2026_05_29_223150_create_audit_logs_table	1
9	2026_05_30_170210_add_user_management_fields_to_users_table	1
10	2026_05_30_191543_make_model_type_and_id_nullable_in_audit_logs	1
11	2026_06_02_030908_create_materia_prima_table	1
12	2026_06_02_030943_create_movimientos_materia_prima_table	1
13	2026_06_02_034908_create_recetas_table	1
14	2026_06_02_041126_create_producciones_table	1
15	2026_06_02_041142_create_consumos_materia_prima_table	1
16	2026_06_07_071759_add_traceability_fields_to_materia_prima_table	1
17	2026_06_07_073910_add_production_waste_fields_to_producciones_table	1
18	2026_06_07_074643_add_produccion_id_to_mermas_table	1
19	2026_06_07_161614_create_controles_calidad_table	1
20	2026_06_08_175242_add_fecha_movimiento_to_movimientos_materia_prima_table	1
\.


--
-- Data for Name: movimientos_materia_prima; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.movimientos_materia_prima (id, materia_prima_id, tipo, cantidad, motivo, referencia_tipo, referencia_id, costo_unitario_momento, observaciones, usuario_id, created_at, updated_at, fecha_movimiento) FROM stdin;
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: producciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.producciones (id, lote, producto_id, cantidad_producida, fecha_produccion, observaciones, usuario_id, created_at, updated_at, materia_prima_desechada, producto_desechado, calidad_observaciones, eficiencia) FROM stdin;
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.products (id, nombre, sku, categoria, stock_actual, unidad, precio_unitario, stock_minimo, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: recetas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.recetas (id, producto_id, materia_prima_id, cantidad_necesaria, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
oPzNRBh3RJCFqCeZxslal1cZLOirZ86Om0xhzs8U	\N	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0	eyJfdG9rZW4iOiJzTGExRDVpTDJNbTNRNG5Bb0I5VUpGdjBZdDBFQXFCWUVraFNGYU01IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==	1780941920
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, role, remember_token, created_at, updated_at, status, requested_role, approved_by, approved_at, rejection_reason) FROM stdin;
\.


--
-- Name: audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.audit_logs_id_seq', 1, false);


--
-- Name: consumos_materia_prima_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.consumos_materia_prima_id_seq', 1, false);


--
-- Name: controles_calidad_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.controles_calidad_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: materia_prima_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.materia_prima_id_seq', 1, false);


--
-- Name: mermas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.mermas_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 20, true);


--
-- Name: movimientos_materia_prima_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.movimientos_materia_prima_id_seq', 1, false);


--
-- Name: producciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.producciones_id_seq', 1, false);


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.products_id_seq', 1, false);


--
-- Name: recetas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.recetas_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 1, false);


--
-- Name: audit_logs audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: consumos_materia_prima consumos_materia_prima_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consumos_materia_prima
    ADD CONSTRAINT consumos_materia_prima_pkey PRIMARY KEY (id);


--
-- Name: controles_calidad controles_calidad_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.controles_calidad
    ADD CONSTRAINT controles_calidad_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: materia_prima materia_prima_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.materia_prima
    ADD CONSTRAINT materia_prima_pkey PRIMARY KEY (id);


--
-- Name: materia_prima materia_prima_sku_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.materia_prima
    ADD CONSTRAINT materia_prima_sku_unique UNIQUE (sku);


--
-- Name: mermas mermas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mermas
    ADD CONSTRAINT mermas_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: movimientos_materia_prima movimientos_materia_prima_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.movimientos_materia_prima
    ADD CONSTRAINT movimientos_materia_prima_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: producciones producciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producciones
    ADD CONSTRAINT producciones_pkey PRIMARY KEY (id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: products products_sku_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_sku_unique UNIQUE (sku);


--
-- Name: recetas recetas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_pkey PRIMARY KEY (id);


--
-- Name: recetas recetas_producto_id_materia_prima_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_producto_id_materia_prima_id_unique UNIQUE (producto_id, materia_prima_id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: movimientos_materia_prima_materia_prima_id_created_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX movimientos_materia_prima_materia_prima_id_created_at_index ON public.movimientos_materia_prima USING btree (materia_prima_id, created_at);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: audit_logs audit_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: consumos_materia_prima consumos_materia_prima_materia_prima_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consumos_materia_prima
    ADD CONSTRAINT consumos_materia_prima_materia_prima_id_foreign FOREIGN KEY (materia_prima_id) REFERENCES public.materia_prima(id);


--
-- Name: consumos_materia_prima consumos_materia_prima_produccion_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consumos_materia_prima
    ADD CONSTRAINT consumos_materia_prima_produccion_id_foreign FOREIGN KEY (produccion_id) REFERENCES public.producciones(id) ON DELETE CASCADE;


--
-- Name: controles_calidad controles_calidad_inspector_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.controles_calidad
    ADD CONSTRAINT controles_calidad_inspector_id_foreign FOREIGN KEY (inspector_id) REFERENCES public.users(id);


--
-- Name: controles_calidad controles_calidad_produccion_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.controles_calidad
    ADD CONSTRAINT controles_calidad_produccion_id_foreign FOREIGN KEY (produccion_id) REFERENCES public.producciones(id) ON DELETE CASCADE;


--
-- Name: controles_calidad controles_calidad_producto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.controles_calidad
    ADD CONSTRAINT controles_calidad_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: mermas mermas_produccion_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mermas
    ADD CONSTRAINT mermas_produccion_id_foreign FOREIGN KEY (produccion_id) REFERENCES public.producciones(id) ON DELETE SET NULL;


--
-- Name: mermas mermas_producto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mermas
    ADD CONSTRAINT mermas_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: mermas mermas_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mermas
    ADD CONSTRAINT mermas_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: movimientos_materia_prima movimientos_materia_prima_materia_prima_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.movimientos_materia_prima
    ADD CONSTRAINT movimientos_materia_prima_materia_prima_id_foreign FOREIGN KEY (materia_prima_id) REFERENCES public.materia_prima(id) ON DELETE CASCADE;


--
-- Name: movimientos_materia_prima movimientos_materia_prima_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.movimientos_materia_prima
    ADD CONSTRAINT movimientos_materia_prima_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id);


--
-- Name: producciones producciones_producto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producciones
    ADD CONSTRAINT producciones_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: producciones producciones_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producciones
    ADD CONSTRAINT producciones_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id);


--
-- Name: recetas recetas_materia_prima_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_materia_prima_id_foreign FOREIGN KEY (materia_prima_id) REFERENCES public.materia_prima(id) ON DELETE CASCADE;


--
-- Name: recetas recetas_producto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: users users_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES public.users(id);


--
-- PostgreSQL database dump complete
--

\unrestrict EML2HwCFcEUgyaU9QD8h4HoCAUAaFRbJrcikmF7jTsLDeOWUv0DXUn6Pkl40lu2

