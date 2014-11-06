/**
 * Automatic image downloader for the urls in the database.
 */

import anorm._
import java.sql.DriverManager
import sys.process._
import java.net.URL
import java.io.File

object ImageDownloader extends App {
	implicit val con = DriverManager.getConnection("jdbc:mysql://localhost:3306/music_shop","root","")
	val count = SQL("SELECT Count(*) As c FROM products")().head[Long]("c").toInt
	val urls = SQL("SELECT product_img FROM products ORDER BY product_id")()
		.map { row => row[String]("product_img") }
	
	mkDirs
	getImg(1650)
	
	def mkDirs {
		val DirectoryPattern = """.+[\/]""".r
		urls.foreach { url =>
			val dir = DirectoryPattern.findFirstIn(url).get
			val file = new File("images/"+dir)
			file.mkdirs()
		}
	}
		
	
	def getImg(index: Int = 0) {
		val url = urls(index)
		(new URL("http://www.stevesmusic.com/" + url) #> new File("images/" + url) !!)
		Thread.sleep((Math.random() * 40).toInt*100)
		if(index < count) getImg(index + 1) 
	}
	
	con.close()
	
	println("done!")
	
	
		
}