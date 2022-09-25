insert into `weekdays` (`id`,`name`,`longname`) values 
  ('1','Dom','Domingo'),('2','Seg','Segunda'),('3','Ter','Terça'),('4','Qua','Quarta'),('5','Qui','Quinta'),('6','Sex','Sexta'),('7','Sáb','Sábado');
insert into `roomtype` (`id`,`type`,`longname`) values 
  ('1','Teo','Teórica'),
  ('2','Lab','Laboratório'),
  ('3','Inf','Recursos Computacionais'),
  ('4','Mix','Mixta Lab/Teoria');

insert into `status` (`id`,`status`,`desc`,`color`) values 
  ('1','tbd','to be done','black'),
  ('2','working on','working on','grey'),
  ('3','review','to be reviewed','yellow'),
  ('4','attention','needs Attention!!','red'),
  ('5','ERR','Error !!','red'),
  ('6','OK','OK','green'),
  ('7','checked','verified/done','blue');

insert into `building` (`id`,`acronym`,`name`,`location`,`mark`) values 
  ('1','undef','não definido','não definido','0'),
  ('2','Eletro','Instituto Eletrotécnico','Campus Centro','1'),
  ('3','Mecânica','Prédio da Mecânica','Campus Centro','1'),
  ('4','Centenário','Prédio Histórico da Eng.','Campus Centro','0'),
  ('5','Eng. nova','Prédio da Eng. Nova','Campus Centro','0'),
  ('6','Arquitetura','Prédio Arquitetura','Campus Centro','0'),
  ('7','Anexo III','Anexo à Reitoria III','Campus Centro','0'),
  ('8','icbs','Prédio ICBS','Campus Centro','0'),
  ('9','faced','Prédio Fac. Educação','Campus Centro','0'),
  ('10','11101','Prédio 11101','Campus Centro','0'),
  ('11','11109','Prédio 11109','Campus Centro','0'),
  ('12','11209','Prédio 11209','Campus Centro','0'),
  ('13','Saúde','Campus Médico','Campus Médico','0'),
  ('14','Vale','Campus do Vale','Campus do Vale','0');
  
insert into `room` (`building_id`,`acronym`,`name`) VALUES
  ('1','undef','não definido'),
  ('2','undef','não definido'),
  ('3','undef','não definido'),
  ('4','undef','não definido'),
  ('5','undef','não definido'),
  ('6','undef','não definido'),
  ('7','undef','não definido'),
  ('8','undef','não definido'),
  ('9','undef','não definido'),
  ('10','undef','não definido'),
  ('11','undef','não definido'),
  ('12','undef','não definido'),
  ('13','undef','não definido'),
  ('14','undef','não definido');
  
  
insert into `unit` (`acronym`,`code`,`longname`,`iscourse`,`isdept`,`mark`) values 
  ('ADMIN','-A-','Admin. place holder',0,0,0),
  ('OTHERS','-O-','Others place holder',0,0,0),
  ('DELAE','ENG10','Departamento de Sistemas Elétricos de Automação e Energia',0,1,1),
  ('DEMEC','ENG03','Departamento de Engenharia Mecânica',0,1,1),
  ('DEQUI','ENG07','Departamento de Engenharia Química',0,1,0),
  ('CCA','CCA99','Eng. de Controle e Automação',1,0,0),
  ('ENE','EEN99','Eng. de Energia',1,0,0),
  ('ECP','ECP99','Eng. de Computação',1,0,0),
  ('ELE','ELE99','Eng. Elétrica',1,0,0),
  ('MEC','MEC99','Eng. Mecânica',1,0,0),
  ('DMPA','MAT01','Departamento de Matemática Pura e Aplicada',0,1,0),
  ('INA','INF01','Departamento de Informática Aplicada',0,1,0),
  ('INT','INF05','Departamento de Informática Teórica',0,1,0),
  ('DEFIS','FIS01','Departamento de Física',0,1,0),
  ('DCA','ADM01','Departamento de Ciências Administrativas',0,1,0),
  ('DARQ','ARQ03','Departamento de Arquitetura',0,1,0),
  ('DERI','ECO02','Departamento de Economia e Relações Internacionais',0,1,0),
  ('DECIV','ENG01','Departamento de Engenharia Civil',0,1,0),
  ('DEMAT','ENG02','Departamento de Engenharia dos Materiais',0,1,0),
  ('DELET','ENG04','Departamento de Engenharia Elétrica',0,1,1),
  ('DEMET','ENG06','Departamento de Metalurgia',0,1,0),
  ('DEPROT','ENG09','Departamento de Engenharia de Produção e Transportes',0,1,0),
  ('DEESP','EDU03','Departamento de Estudos Especializados',0,1,0),
  ('DESOC','HUM04','Departamento de Sociologia',0,1,0),
  ('DHH','IPH01','Departamento de Hidromecânica e Hidrologia',0,1,0),
  ('DOH','IPH02','Departamento de Obras Hidráulicas',0,1,0),
  ('DLM','LET02','Departamento de Línguas Modernas',0,1,0),
  ('DECOL','BIO11','Departamento de Ecologia',0,1,0),
  ('DEST','MAT02','Departamento de Estatística',0,1,0),
  ('DEPMSOC','MED05','Departamento de Medicina Social',0,1,0),
  ('DQI','QUI01','Departamento de Química Inorgânica',0,1,0),
  ('CGQUI','QUI99','Comissão de Graduação de Química',0,1,0),
  ('DDET','DIR04','Departamento de Direito Econômico e do Trabalho',0,1,0),
  ('DEA','FIS02','Departamento de Astronomia',0,1,0);
  
insert into `profkind` (`id`,`name`,`longname`) values 
  ('1','DE','Dedicação Exclusiva'),
  ('2','20h','20 Horas'),
  ('3','40h','40 Horas'),
  ('4','Subs','Substituto(a)');
insert into `term` (`id`,`code`,`name`) values 
  ('1','Etp.01','Etapa 01'),('2','Etp.02','Etapa 02'),('3','Etp.03','Etapa 03'),
  ('4','Etp.04','Etapa 04'),('5','Etp.05','Etapa 05'),('6','Etp.06','Etapa 06'),
  ('7','Etp.07','Etapa 07'),('8','Etp.08','Etapa 08'),('9','Etp.09','Etapa 09'),
  ('10','Etp.10','Etapa 10'),('33','EL','Eletivas');
insert into `role` (`rolename`,`description`,`isadmin`,`can_edit`,`can_dupsem`,`chg_vacancies`,`chg_class`,`can_viewlog`,`chg_disciplines`,`chg_coursedisciplines`,`unit_id`)
 values
       ('admin','Administrator account','1','1','1','1','1','1','1','1','1'), 
('delae-f','Dept. account DELAE (full)','0','1','0','1','1','0','1','0','3'), 
         ('delae','Dept. account DELAE','0','1','1','1','1','0','0','0','3'), 
('demec-f','Dept. account DEMEC (full)','0','1','0','1','1','0','1','0','4'), 
         ('demec','Dept. account DEMEC','0','1','0','1','1','0','0','0','4'), 
('dequi-f','Dept. account DEQUI (full)','0','1','0','1','1','0','1','0','5'), 
         ('dequi','Dept. account DEQUI','0','1','0','1','1','0','0','0','5'), 
  ('cca-f','COMGRAD account CCA (full)','0','1','0','1','1','0','0','1','6'), 
           ('cca','COMGRAD account CCA','0','1','0','1','1','0','0','0','6'), 
  ('ene-f','COMGRAD account ENE (full)','0','1','0','1','1','0','0','1','7'), 
           ('ene','COMGRAD account ENE','0','1','0','1','1','0','0','0','7'), 
  ('ecp-f','COMGRAD account ECP (full)','0','1','0','1','1','0','0','1','8'), 
           ('ecp','COMGRAD account ECP','0','1','0','1','1','0','0','0','8'), 
 ('prof(a)','Prof. account (view only)','0','0','0','0','0','0','0','0','2'), 
   ('sec(a)','Sec. account (view only)','0','0','0','0','0','0','0','0','2'); 
    
  
insert into `loglevel` (`level`,`str`,`description`) VALUES
  ('INFO','information level','auxiliary/informational log level'),
  ('DEBUG','debug level','extra log for debuging'),
  ('TRACE','trace level','extra log for debuging/tracing'),
  ('WARNING','warning level','might be a problem'),
  ('EDIT','DB changing','user issued a DB changing action'),
  ('DBERROR','a DB error occurred','debug/tracing'),
  ('LOGIN','user login','info level');
  
  
insert into `disciplinekind` (`code`,`longname`) VALUES
  ('OB','obrigatória'),
  ('EL','eletiva'),
  ('AL','obrigatória alternativa'),
  ('AD','adicional');
  

insert into `discipline` (`dept_id`,`code`,`long_name`,`Lcred`,`Tcred`) values ((select `id` from `coursedept` where `coursedept`.`acronym` = 'delae'), 'test01','disciplina de teste','2','4');




insert into `accrole` (`account_id`,`role_id`) VALUES
  ('1','1'),
  ('1','2'),
  ('1','3'),
  ('1','5'),
  ('1','9');
