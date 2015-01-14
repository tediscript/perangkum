<?php

namespace Summarizer;

use Sastrawi\SentenceDetector\SentenceDetectorFactory;
use Sastrawi\Tokenizer\TokenizerFactory;

/**
 * Summarize main sentences from given paragraph
 *
 * @author tediscript
 */
class Summarizer
{

    /**
     * @var string source
     * */
    protected $source = '';

    /**
     * @var Stemmer stemmer
     */
    protected $stemmer;

    /**
     * @var StopWordRemover stopWordRemover
     * */
    protected $stopWordRemover;

    /**
     * @var \Sastrawi\SentenceDetector\SentenceDetectorInterface
     * */
    protected $sentenceDetector;

    /**
     * @var \Sastrawi\Tokenizer\DefaultTokenizer
     * */
    protected $tokenizer;

    /**
     * Constructor
     *
     * @return void
     * */
    public function __construct()
    {
        $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
        $this->stemmer = $stemmerFactory->createStemmer();

        $stopWordRemoverFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
        $this->StopWordRemover = $stopWordRemoverFactory->createStopWordRemover();

        $sentenceDetectorFactory = new SentenceDetectorFactory();
        $this->sentenceDetector  = $sentenceDetectorFactory->createSentenceDetector();

        $tokenizerFactory = new TokenizerFactory();
        $this->tokenizer  = $tokenizerFactory->createDefaultTokenizer();
    }

    /**
     * Summarize from string
     *
     * @param string $source Source to summarize
     * @param int $strictness Level of strictness
     * @return array Summarized sentences
     * */
    public function summarize($source = '', $strictness = 1)
    {
        $dataBag = array();

        $this->source = $source;
        $segmentedParagraphs = $this->segmentingParagraph($source);
        $dataBag['segmentedParagraphs'] = $segmentedParagraphs;

        $segmentedSentences = array();
        foreach ($segmentedParagraphs as $paragraph) {
            $sentences = $this->segmentingSentence($paragraph);
            if (empty($sentences)) {
                continue;
            }
            $segmentedSentences = array_merge($segmentedSentences, $sentences);
        }
        $dataBag['segmentedSentences'] = $segmentedSentences;

        $stemmedSentences = $this->stemming($segmentedSentences);
        $dataBag['stemmedSentences'] = $stemmedSentences;

        $sentencesWithoutStopWords = $this->StopWordRemover->remove($stemmedSentences);
        $dataBag['sentencesWithoutStopWords'] = $sentencesWithoutStopWords;

        $wordModus = $this->getWordModus($sentencesWithoutStopWords);
        $dataBag['wordModus'] = $wordModus;

        $mainSentences = $this->getMainSentences($segmentedSentences, $wordModus, $strictness);
        $dataBag['mainSentences'] = $mainSentences;

//        return $dataBag;
        return $mainSentences;
    }

    /**
     * Get main sentence of paragraph
     * 
     * @param type $sentences
     * @param type $wordModus
     * @param type $strictness
     * @return type
     */
    protected function getMainSentences($sentences = array(), $wordModus = array(), $strictness = 1)
    {
        $summaries = array();

        $topWords = array();

        foreach ($wordModus as $k => $v) {
            $topWords[] = $k;
            if (count($topWords) === $strictness) {
                break;
            }
        }

        foreach ($sentences as $sentence) {
            //first sentence is a must
            if (empty($summaries)) {
                $summaries[] = $sentence;
                continue;
            }

            $s = strtolower($sentence);
            if ($this->contains($s, $topWords)) {
                $summaries[] = $sentence;
            }
        }

        return $summaries;
    }

    /**
     * Get array of words. Sort in modus
     * 
     * @param type $string
     * @return int
     */
    protected function getWordModus($string = '')
    {
        $wordModus = array();

        $words = $this->tokenize($string);
        foreach ($words as $word) {
            if (is_numeric($word)) {
                continue;
            }

            if (isset($wordModus[$word])) {
                $wordModus[$word] += 1;
            } else {
                $wordModus[$word] = 1;
            }
        }

        arsort($wordModus, SORT_NUMERIC);

        return $wordModus;
    }

    /**
     * Stem a text string into stemmed text
     *
     * @param  string $source the text string to stem
     * @return string stemmed text
     */
    protected function stemming($source)
    {
        $result = '';
        if (is_array($source)) {
            foreach ($source as $sentence) {
                $result = trim($result . ' ' . $this->stemmer->stem($sentence));
            }
        } else {
            $result = $this->stemmer->stem($source);
        }

        return $result;
    }

    /**
     * Segmenting string source to array paragraphs
     * 
     * @param string $source
     * @return array paragraphs
     */
    protected function segmentingParagraph($source)
    {
        $paragraphs = array();
        $arr = array_map('trim', explode("\n", $source));
        foreach ($arr as &$paragraph) {
            if (!empty($paragraph)) {
                $paragraphs[] = $paragraph;
            }
        }

        return $paragraphs;
    }

    /**
     * Segmenting paragraph to array sentences
     * Pemecahan menggunakan Sastrawi Sentence Detector.
     * 
     * @param string $paragraph
     * @return array sentences
     */
    protected function segmentingSentence($paragraph)
    {
        $sentences = array_map('trim', $this->sentenceDetector->detect($paragraph));

        return $sentences;
    }

    /**
     * Tokenize string using Sastrawi Tokenizer
     * 
     * @param string $string
     * @return array tokenized string
     */
    protected function tokenize($string = '')
    {
        $tokens = $this->tokenizer->tokenize($string);

        return $tokens;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

}
