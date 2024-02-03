insert into `weekdays` (`id`,`abrv`,`name`) values 
  ('1','Dom','Domingo'),('2','Seg','Segunda'),('3','Ter','Terça'),('4','Qua','Quarta'),('5','Qui','Quinta'),('6','Sex','Sexta'),('7','Sáb','Sábado');
insert into `roomtype` (`id`,`acronym`,`name`) values 
  ('1','Teo','Sala de Aula'),
  ('2','Lab','Laboratório'),
  ('3','Inf','Sala Recursos Computacionais'),
  ('4','Mix','Sala Mixta Lab/Teoria');

insert into `status` (`id`,`status`,`desc`,`color`) values 
  ('1','---','---','white'),
  ('2','working on','working on','grey'),
  ('3','review','in review','yellow'),
  ('4','attention','needs Attention!!','red'),
  ('5','ERR','Error !!','red'),
  ('6','OK','OK','green'),
  ('7','checked','verified/done','blue'),
  ('8','dup','duplicated','teal');

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
  ('10','Economia','Prédio Fac. Economia','Campus Centro','0'),
  ('11','Prédio Branco','Prédio Branco','Campus Centro','0'),
  ('12','Saúde','Campus Médico','Campus Médico','0'),
  ('13','Vale','Campus do Vale','Campus do Vale','0'),
  ('14','observatorio','Antigo Observatório','Campus Centro','0')
  ;
  
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
  
  
insert into `unit` (`id`,`acronym`,`code`,`name`,`iscourse`,`isdept`,`mark`) values 
  ('1','ADMIN','-A-','Admin. place holder',0,0,0),
  ('2','OTHERS','-O-','Others place holder',0,0,0),
  ('3','DELAE','ENG10','Departamento de Sistemas Elétricos de Automação e Energia',0,1,1),
  ('4','DEMEC','ENG03','Departamento de Engenharia Mecânica',0,1,1),
  ('5','DEQUI','ENG07','Departamento de Engenharia Química',0,1,0),
  ('6','Eng.CCA','CCA99','Eng. de Controle e Automação',1,0,1),
  ('7','Eng.ENE','EEN99','Eng. de Energia',1,0,1),
  ('8','Eng.ECP','ECP99','Eng. de Computação',1,0,1),
  ('9','Eng.ELE','ELE99','Eng. Elétrica',1,0,1),
  ('10','Eng.MEC','MEC99','Eng. Mecânica',1,0,1),
  ('11','DMPA','MAT01','Departamento de Matemática Pura e Aplicada',0,1,0),
  ('12','INA','INF01','Departamento de Informática Aplicada',0,1,0),
  ('13','INT','INF05','Departamento de Informática Teórica',0,1,0),
  ('14','DEFIS','FIS01','Departamento de Física',0,1,0),
  ('15','DCA','ADM01','Departamento de Ciências Administrativas',0,1,0),
  ('16','DEG','ARQ03','Departamento de Desgin e Expressão Gráfica',0,1,0),
  ('17','DERI','ECO02','Departamento de Economia e Relações Internacionais',0,1,0),
  ('18','DECIV','ENG01','Departamento de Engenharia Civil',0,1,0),
  ('19','DEMAT','ENG02','Departamento de Engenharia dos Materiais',0,1,0),
  ('20','DELET','ENG04','Departamento de Engenharia Elétrica',0,1,1),
  ('21','DEMET','ENG06','Departamento de Metalurgia',0,1,0),
  ('22','DEPROT','ENG09','Departamento de Engenharia de Produção e Transportes',0,1,0),
  ('23','DEESP','EDU03','Departamento de Estudos Especializados',0,1,0),
  ('24','DESOC','HUM04','Departamento de Sociologia',0,1,0),
  ('25','DHH','IPH01','Departamento de Hidromecânica e Hidrologia',0,1,0),
  ('26','DOH','IPH02','Departamento de Obras Hidráulicas',0,1,0),
  ('27','DLM','LET02','Departamento de Línguas Modernas',0,1,0),
  ('28','DECOL','BIO11','Departamento de Ecologia',0,1,0),
  ('29','DEST','MAT02','Departamento de Estatística',0,1,0),
  ('30','DEPMSOC','MED05','Departamento de Medicina Social',0,1,0),
  ('31','DQI','QUI01','Departamento de Química Inorgânica',0,1,0),
  ('32','CGQUI','QUI99','Comissão de Graduação de Química',0,1,0),
  ('33','DDET','DIR04','Departamento de Direito Econômico e do Trabalho',0,1,0),
  ('34','DEA','FIS02','Departamento de Astronomia',0,1,0),
  ('35','DH','HUM03','Departamento de História',0,1,0),
  ('36','Eng.AMB','AMB99','Eng. Ambiental',1,0,0),
  ('37','PPGEE','ELExx','Prog. Pós-Graduação Eng. Elétrica',1,0,1),
  ('38','PPGIE','PEI00','Prog. Pós-Graduação em Informática na Educação',1,0,1),
  ('39','DEGD','GEO05','Departamento de Geodésia',0,1,0),
  ('40','DEBOT','BIO02','Departamento de Botânica',0,1,0),
  ('41','DFQ','QUI03','Departamento de Físico-Química',0,1,0),
  ('42','DMP','GEO03','Departamento de Mineralogia e Petrologia',0,1,0),
  ('43','DEMIN','ENG05','Departamento de Engenharia de Minas',0,1,0),
  ('44','DCP','DIR01','Departamento de Ciências Penais',0,1,0),
  ('45','DEMIC','CBS06','Departamento de Microbiologia, Imunologia e Parasitologia',0,1,0),
  ('46','DEGEG','GEO01','Departamento de Geografia',0,1,0),
  ('47','DQO','QUI02','Departamento de Química Orgânica',0,1,0),
  ('48','VAERE','VAERE','Vínculo Acadêmico - ERE',0,1,0),
  ('49','PROMEC','MECxx','Prog. Pós-Graduação Eng. Mecânica',1,0,1),
  ('50','Eng.CIV','CIV99','Eng. Civil',1,0,1),
  ('51','Eng.MAT','MAT99','Eng. Materiais',1,0,1),
  ('52','Eng.MIN','MIN99','Eng. Minas',1,0,1),
  ('53','Eng.ALI','ITA99','Eng. Alimentos',1,0,1),
  ('54','Eng.QUI','QUI9x','Eng. Química',1,0,1),
  ('55','Eng.MET','MET99','Eng. Metalúrgica',1,0,1),
  ('56','Eng.PROD','PRO99','Eng. Produção',1,0,1);
  
  
insert into `coursedept` (`course_id`,`dept_id`) values
  ('6','3'),
  ('6','4'),
  ('7','3'),
  ('7','4'),
  ('8','3'),
  ('8','12'),
  ('8','13'),
  ('9','20'),
  ('10','4'),
  ('37','3'),
  ('37','20'),
  ('49','4');
  
insert into `profkind` (`id`,`acronym`,`name`) values 
  ('1','DE','Dedicação Exclusiva'),
  ('2','20h','20 Horas'),
  ('3','40h','40 Horas'),
  ('4','Subs','Substituto(a)'),
  ('5','-none-','Inativo(a)');
  
insert into `term` (`id`,`code`,`name`) values 
  ('1','Etp.01','Etapa 01'),('2','Etp.02','Etapa 02'),('3','Etp.03','Etapa 03'),
  ('4','Etp.04','Etapa 04'),('5','Etp.05','Etapa 05'),('6','Etp.06','Etapa 06'),
  ('7','Etp.07','Etapa 07'),('8','Etp.08','Etapa 08'),('9','Etp.09','Etapa 09'),
  ('10','Etp.10','Etapa 10'),('33','EL','Eletivas');
insert into `role` (`rolename`,`description`,`isadmin`,`can_edit`,`can_dupsem`,`can_class`,`can_addclass`,`can_vacancies`,`can_disciplines`,`can_coursedisciplines`,`can_prof`,`can_room`,`can_viewlog`,`unit_id`)
 values
       ('admin','Administrator account','1','1','1','1','1','1','1','1','1','1','1','1'), 
('delae-f','Dept. account DELAE (full)','0','1','0','1','1','1','1','0','1','1','0','3'), 
         ('delae','Dept. account DELAE','0','1','0','1','0','1','0','0','0','0','0','3'), 
('demec-f','Dept. account DEMEC (full)','0','1','0','1','1','1','1','0','1','1','0','4'), 
         ('demec','Dept. account DEMEC','0','1','0','1','0','1','0','0','0','0','0','4'), 
('dequi-f','Dept. account DEQUI (full)','0','1','0','1','1','1','1','0','1','1','0','5'), 
         ('dequi','Dept. account DEQUI','0','1','0','1','0','1','0','0','0','0','0','5'), 
  ('cca-f','COMGRAD account CCA (full)','0','1','0','1','1','1','1','1','0','0','0','6'), 
           ('cca','COMGRAD account CCA','0','1','0','1','0','1','0','0','0','0','0','6'), 
  ('ene-f','COMGRAD account ENE (full)','0','1','0','1','1','1','1','1','0','0','0','7'), 
           ('ene','COMGRAD account ENE','0','1','0','1','0','1','0','0','0','0','0','7'), 
  ('ecp-f','COMGRAD account ECP (full)','0','1','0','1','1','1','1','1','0','0','0','8'), 
           ('ecp','COMGRAD account ECP','0','1','0','1','0','1','0','0','0','0','0','8'), 
 ('prof(a)','Prof. account (view only)','0','0','0','0','0','0','0','0','0','0','0','2'), 
   ('sec(a)','Sec. account (view only)','0','0','0','0','0','0','0','0','0','0','0','2'); 
    
  
insert into `loglevel` (`level`,`str`,`description`) VALUES
  ('INFO','information level','auxiliary/informational log level'),
  ('DEBUG','debug level','extra log for debuging'),
  ('TRACE','trace level','extra log for debuging/tracing'),
  ('WARNING','warning level','might be a problem'),
  ('EDIT','DB changing','user issued a DB changing action'),
  ('DBERROR','a DB error occurred','debug/tracing'),
  ('LOGIN','user login','info level');
  
  
insert into `disciplinekind` (`id`,`code`,`name`) VALUES
  ('1','OB','obrigatória'),
  ('2','EL','eletiva'),
  ('3','AL','obrigatória alternativa'),
  ('4','AD','adicional');
  
  
insert into `account` (`id`,`email`,`password`,`activ`,`name`) VALUES
	('1','admin@c','c','1','test admin'),
	('2','delae-f@c','c','1','test delae-f'),
	('3','delae@c','c','1','test delae'),
	('4','cca-f@c','c','1','test cca-f'),
	('5','cca@c','c','1','test cca');
	
insert into `accrole` (`account_id`,`role_id`) VALUES
	('1','1'),
	('2','2'),
	('3','3'),
	('4','8'),
	('5','9');
	
