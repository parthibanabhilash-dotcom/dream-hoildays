INSERT IGNORE INTO gallery_categories(name) VALUES ('Trips'),('Destinations'),('Vehicles');
INSERT IGNORE INTO pages(slug,title,body,meta_description,status) VALUES
('about','About The Dream Holidays','Company introduction, travel approach, team and service locations await business approval.','Learn about The Dream Holidays.','draft'),
('privacy','Privacy Policy','DRAFT — Business and legal review required. Document data collected through website enquiries, purposes, recipients, retention, deletion requests, security and third-party WhatsApp links.','Privacy information for The Dream Holidays.','draft'),
('terms','Terms and Conditions','DRAFT — Business and legal review required. Document quotation acceptance, customer responsibilities and applicable travel terms.','Terms for The Dream Holidays services.','draft'),
('cancellation','Cancellation and Refund Policy','DRAFT — Business and legal review required. Confirm cancellation deadlines, supplier rules, charges and refund timelines.','Cancellation and refund information.','draft');
INSERT IGNORE INTO services(slug,name,description,status) VALUES
('family-tours','Family tours','DRAFT — Add approved family tour details.','draft'),
('honeymoon-tours','Honeymoon tours','DRAFT — Add approved honeymoon tour details.','draft'),
('college-industrial-visits','College industrial visits','DRAFT — Add approved industrial visit details.','draft'),
('school-trips','School trips','DRAFT — Add approved school trip details.','draft'),
('corporate-tours','Corporate tours','DRAFT — Add approved corporate tour details.','draft'),
('devotional-tours','Devotional tours','DRAFT — Add approved devotional tour details.','draft'),
('adventure-tours','Adventure tours','DRAFT — Add approved adventure tour details.','draft');
INSERT IGNORE INTO packages(slug,name,destination,category,duration_days,trip_type,overview,accommodation,transport,meals,inclusions,exclusions,cancellation,status) VALUES
('ooty','Ooty — draft','Ooty','domestic',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('kodaikanal','Kodaikanal — draft','Kodaikanal','domestic',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('munnar','Munnar — draft','Munnar','domestic',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('wayanad','Wayanad — draft','Wayanad','domestic',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('coorg','Coorg — draft','Coorg','domestic',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('goa','Goa — draft','Goa','domestic',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('manali','Manali — draft','Manali','domestic',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('andaman','Andaman — draft','Andaman','domestic',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('bali','Bali — draft','Bali','international',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('thailand','Thailand — draft','Thailand','international',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('singapore','Singapore — draft','Singapore','international',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft'),
('dubai','Dubai — draft','Dubai','international',1,'Family','DRAFT — duration and all package facts require approval.','','','','','','','draft');
