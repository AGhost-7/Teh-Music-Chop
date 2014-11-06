/**
 * This contains data from an old database that had some
 * guitar models that I extracted from the Fender website.
 */
object FromOldDBToNewDb {
	private val str = """('Vintage Modified Surf Stratocaster','0301220','$499.99',2,1,NULL),
('Vintage Modified Surf Stratocaster','0301220','$499.99',2,1,NULL),
('Vintage Modified Telecaster Special','0301250','$499.99',2,1,NULL),
('Vintage Modified Cabronita Telecaster','0301270','$499.99',2,1,NULL),
('Vintage Modified Tele Custom','032750','$399.99',2,1,NULL),
('Vintage Modified Tele Custom II','032760','$479.99',2,1,NULL),
('Vintage Modified Jaguar HH','030270','$499.99',2,1,NULL),
('Vintage Modified Jazzmaster Special','030280','$499.99',2,1,NULL),
('Jagmasterâ¢','032070','$499.99',2,1,NULL),
('Vintage Modified Telecaster Deluxe','0301265','$499.99',2,1,NULL),
('Vintage Modified Telecaster Custom','0301260','$499.99',2,1,NULL),
('Vintage Modified Jaguar','0302000','$499.99',2,1,NULL),
('Vintage Modified Jazzmaster','0302100','$499.99',2,1,NULL),
('Jim Adkins JA-90 Telecaster Thinline','026235','$999.99',2,1,NULL),
('Chris Shiflett Telecaster Deluxe','0142400780','$899.99',2,1,NULL),
('Cabronita Telecaster Thinline','0145502','$999.99',2,1,NULL),
('Cabronita Telecaster','0145402','$799.99',2,1,NULL),
('Deluxe Stratocaster HSS Plus Top with iOS Connectivity','0144732','$849.99',2,1,NULL),
('Deluxe Players Strat','013300','$929.99',2,1,NULL),
('Deluxe Nashville Tele','013530','$849.99',2,1,NULL),
('Deluxe Lone Starâ¢ Stratocaster','014503','$899.99',2,1,NULL),
('Deluxe Roadhouseâ¢ Stratocaster','014501','$899.99',2,1,NULL),
('Special Edition Custom Telecaster FMT HH','026200','$839.99',2,1,NULL),
('Black Paisley Stratocaster HSS','0140073506','$669.99',2,1,NULL),
('Blacktopâ¢ Telecaster Baritone','0148700','$699.99',2,1,NULL),
('Cabronita Precision Bass','0145602','$799.99',2,1,NULL),
('50s Precision Bass','013170','$999.99',2,1,NULL),
('Mustang Bass','025390','$999.99',2,1,NULL),
('Deluxe Dimensionâ¢ Bass IV','014260','$999.99',2,1,NULL),
('Deluxe Active P Bass Special','013576','$949.99',2,1,NULL),
('Deluxe Active Jazz Bass','013676','$974.99',2,1,NULL),
('Blacktopâ¢ Jazz Bass','0148600','$699.99',2,1,NULL),
('Blacktopâ¢ Precision Bass','0148500','$699.99',2,1,NULL),
('Standard Precision Bass','014610','$769.99 - $789.99',2,1,NULL),
('Standard Jazz Bass','014620','$799.99 - $819.99',2,1,NULL),
('Standard Jazz Bass Left-Handed','014622','$799.99 - $819.99',2,1,NULL),
('Standard Jazz Bass Fretless','0146208','$799.99 - $819.99',2,1,NULL)"""
	
	def apply() {
		val SQLPattern = """^(\(')([A-z ]+)(',')(\d+)(','\$)(\d+\.\d{2})(',)(\d+)(,)(\d+)""".r	
		for {
			line <- str.lines
			matched <- SQLPattern findFirstMatchIn line
		} println(s"""('Guitar - ${matched group 2}', 'Fender', ${matched group 6}, ${(Math.random() * 3+1).toInt})""")
	}
}