/**
 * This file contains all of the logic to scrape the website for Steve's music
 * store for data to use in the database. Batteries not included.
 */


package object vars {
	val fromPageToFile = List(
		("http://www.stevesmusic.com/guitars-and-basses/acoustic-guitars", "acoustic-guitars.csv", 22),
		("http://www.stevesmusic.com/guitars-and-basses/electric-guitars", "electric-guitars.csv", 20),
		("http://www.stevesmusic.com/guitars-and-basses/classical-guitars", "classical-guitars.csv", 3),
		("http://www.stevesmusic.com/guitars-and-basses/basses", "bass-guitars.csv", 7),
		("http://www.stevesmusic.com/drums/drumsets", "drum-sets.csv", 5),
		("http://www.stevesmusic.com/drums/hand-percussions", "hand-percussion.csv", 7),
		("http://www.stevesmusic.com/drums/electronic-drums", "electric-drums.csv", 2),
		("http://www.stevesmusic.com/keyboards-controllers/home-keyboards", "keyboards.csv", 1),
		("http://www.stevesmusic.com/keyboards-controllers/keyboards-sound-modules", "keyboards.csv", 5),
		("http://www.stevesmusic.com/ukuleles-ouds/ouds", "ouds.csv", 1),
		("http://www.stevesmusic.com/ukuleles-ouds/ukuleles", "ukuleles.csv", 3),
		("http://www.stevesmusic.com/brass-woodwinds/clarinets", "clarinets.csv", 1),
		("http://www.stevesmusic.com/brass-woodwinds/flutes", "flutes.csv", 1),
		("http://www.stevesmusic.com/brass-woodwinds/trumpets", "trumpets.csv", 1),
		("http://www.stevesmusic.com/violins-banjos-ukuleles.../banjos", "banjos.csv", 1),
		("http://www.stevesmusic.com/violins-banjos-ukuleles.../double-basses", "double-basses.csv", 1),
		("http://www.stevesmusic.com/violins-banjos-ukuleles.../mandolins", "mandolins.csv", 1),
		("http://www.stevesmusic.com/violins-banjos-ukuleles.../violins", "violins.csv", 1),
		("http://www.stevesmusic.com/brass-woodwinds/saxophones", "saxophones.csv", 1),
		("http://www.stevesmusic.com/brass-woodwinds/trumpets", "trumpets.csv", 1),
		("http://www.stevesmusic.com/brass-woodwinds/trombones", "trombones.csv", 1),
		("http://www.stevesmusic.com/amplifiers/acoustic-amps", "acoustic-guitar-amps.csv",1),
		("http://www.stevesmusic.com/amplifiers/guitar-combos", "electric-guitar-amps.csv", 9)
		)
}
/**
 * This is the main
 */
object FromStevesToSQL extends App {
	//ToCSV
	CSVToSQL
}

/**
 * This now takes the csv files and parses everything into SQL.
 */
object CSVToSQL {
	import vars._
	
	implicit val brands = 
		io.Source.fromFile("brands2.txt").getLines.toSeq
		
	//sqlFinal("acoustic-guitars.csv","Acoustic Guitars")
	//sqlFinal("electric-guitars.csv","Electric Guitars")
	//sqlFinal("classical-guitars.csv","Classical Guitars")
	//sqlFinal("bass-guitars.csv","Bass Guitars")
	//sqlFinal("drum-sets.csv","Drum Sets")
	//sqlFinal("hand-percussion.csv","Hand Percussion")
	//sqlFinal("electric-drums.csv","Electric Drums")
	/*(sqlFinal("keyboards.csv","Keyboards") ++
	sqlFinal("ouds.csv","Ouds") ++
	sqlFinal("ukuleles.csv","Ukuleles") ++
	sqlFinal("clarinets.csv","Clarinets") ++
	sqlFinal("flutes.csv","Flutes") ++
	sqlFinal("trumpets.csv","Trumpets") ++
	sqlFinal("banjos.csv","Banjos") ++
	sqlFinal("double-basses.csv","Double Basses"))*/
	(sqlFinal("mandolins.csv","Mandolins") ++
	sqlFinal("violins.csv","Violins") ++
	sqlFinal("saxophones.csv","Saxophones") ++
	sqlFinal("trumpets.csv","Trumpets") ++
	sqlFinal("trombones.csv","Trombones") ++
	sqlFinal("acoustic-guitar-amps.csv","Acoustic Guitar Amps") ++
	sqlFinal("electric-guitar-amps.csv","Electric Guitar Amps"))
		.foreach(s => println(s + ","))
		
	
	def sqlFinal(file: String, category: String)(implicit brands: Seq[String]) = {
		io.Source.fromFile("site-data/" + file)
			.getLines
			.flatMap { line =>
				val sp = line.split(7.toChar)
				val name = sp(0)
				val price = sp(1)
				val img = sp(2)
				val quan = ((Math.random * 2) + (Math.random * 3)).toInt
				brands.find { brand => name.toLowerCase.startsWith(brand.toLowerCase) } match {
					case Some(brand) => Some(s"""('$name','$brand','$category',$price,$quan,'$img')""")
					case None => None//System.err.println(s"Error with $name");None
				}
			}
	}
	
	//brandsToFile
	//brandManufacturers
	def brandManufacturers = {
		val brands = io.Source.fromFile("brands.txt")
			.getLines
			.map { line => 
				val brand = line.split(7.toChar)(0)
				val capped = brand.split(" ")
					.map { s => s.head.toUpper + s.tail.toLowerCase }
					.mkString(" ")
				"('" + capped + "')" 
			}
			.mkString(",\n")
		println(brands)
	}
	
	//withBrands
	def withBrands = {
		val brands = io.Source.fromFile("brands.txt")
			.getLines
			.map { _.split(7.toChar)(0) }
			
		val writer = Writer("products.sql")
		val buf = writer.toBuffer
		buf += "INSERT INTO products" + 
			"(product_name, product_price, product_manufacturer, product_quantity, product_img)\n" +
			"VALUES\n"
		forAllFiles { line =>
			val sp = line.split(7.toChar)
			val name = sp(0)
			val price = sp(1)
			val img = sp(2)
			brands.find { brand => name.toLowerCase.contains(brand.toLowerCase) } match {
				case Some(brand) =>
					buf += s"""('$name', $price, '$brand', ${(Math.random() * 4).toInt}, '$img')\n"""
				case None => 
					System.err.println("Couldn't find brand for " + name)
			}
		}
		buf += ";"	
		buf.done
	}
	
	def forAllFiles(traversor: String => Unit) {
		for((url,targetFile,maxIndex) <- fromPageToFile) {
			io.Source.fromFile("site-data/" + targetFile, "UTF-8")
				.getLines
				.foreach(traversor)
		}
	}
	
	def brandsToFile = {
		import scala.collection.mutable.ListBuffer
		val ls = ListBuffer[(String, String)]()
		
		forAllFiles { line =>
			val sp = line.split(7.toChar)
			val name = sp(0)
			val brand = name.substring(0, name.indexOf(" ")).toLowerCase
			
			if(!ls.exists { case(_, _brand) => brand == _brand } )
				ls += ((name, brand))
			
			try {
				val price = sp(1).toDouble
			} catch {
				case err: Throwable => 					
					sp.foreach(println)
					throw err
			}
			val url = "http://www.stevesmusic.com/" + sp(2)
		}
		
		Writer("brands.txt") { print =>
			ls.sortWith { _._2 < _._2 }
			.foreach { case(name, brand) => print(brand + 7.toChar + name); println("$brand") }
			
		}
	}
	
}

/**
 * Takes the data from pages and places it in a file at the given location.
 */
object ToCSV {
	import scala.xml._
	import scala.io.Source
	import org.ccil.cowan.tagsoup.{ Parser, XMLWriter }
	import java.io.StringWriter 
	import org.xml.sax.{InputSource => XSource} 
	
	import vars._
	
	// regex used for the top table pattern
	private val top = """<table .+ class="tabTable">""".r
	
	for((url, target, maxIndex) <- fromPageToFile)
		try { getPages(url, "site-data/" + target, maxIndex) }
		catch {
			case err: Throwable => System.err.println("Exception at : " + url)
		}
	
	private def getPages(url: String, target: String, maxIndex: Int) {
		val writer = Writer(target)
		getAll(writer, url, maxIndex)
	}
	
	/**
	 * Recursive function which fetches the pages until it reaches maxIndex
	 */
	private def getAll(writer: SimplePrintWriter, url: String, maxIndex: Int = 1, 
			index: Int = 1): Unit = {
		// get the html page...
		val html = Source
			.fromURL(if(index == 1) url else s"$url?sort=2&page=$index")
			.getLines
		
		// just need the table ->contents<-, table headers are skipped as well.
		val table = html
			.dropWhile { top.findFirstIn(_) == None }
			.takeWhile { !_.contains("</table>") }
			.drop(6)
			.mkString("\n")
			
		// Parse the html to xpath readable shit
		val parser = new Parser() 
		val swriter = new StringWriter() 
			
		parser.setContentHandler(new XMLWriter(swriter)) 
		parser.parse(new XSource(new java.io.StringReader(table))) 
	
		val x = XML.loadString(swriter.toString()) 
		
		// Now, we can query the data using xpath and write it into a file
		writer { write =>
			for (row <- x \\ "tr") {
				val name = (row \ "td" \ "h3" \ "a").text
				val img = (row \"td" \ "a" \ "img" \ "@src").toString
				
				try {
					val price = 
						(if((row \ "td" \ "span" \ "span").length == 0) 
							(row \ "td" \ "span").text		
						else
							// in this case, its on special, so the price that we want
							// is wrapped in another span level.
							(row \ "td" \ "span" \ "span")(1).text
						).substring(3).replaceAll(",","")
					val c = 7.toChar // lets use the bell sound character as our delimiter
					write(name + c + price + c + img) 
				} catch {
					case default:Throwable => 
						System.err.println("Unexpected result at row " + name)
				}
			}
		}
			
		// If there are more pages to parse, sleep for a bit (so that uh... it 
		// doesn't look like I'm trying to DOS them) and call self to keep going.
		if(index < maxIndex) {
			Thread.sleep(1000 + (Math.random() * 1000).toInt)
			getAll(writer,url, maxIndex, index + 1)
		}
	}
}
