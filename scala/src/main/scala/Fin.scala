/**
 * I just realized that using the full image path would be cleaner.
 * Should be the last time I do this for the project.
 */
object Fin extends App {
	val pat = """^(.+)(bmz_cache.+)""".r
	var count = 0
	for { 
		line <- io.Source.fromFile("fin.txt").getLines
		m <- pat.findFirstMatchIn(line)
	} {
		count += 1
		println(m.group(1) + "assets/images/products/" + m.group(2))
	}
	println(count + " out of 1767")
}