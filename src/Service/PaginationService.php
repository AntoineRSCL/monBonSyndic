<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationService
{
    /**
     * Le nom de l'entité sur laquelle on veut effectuer une pagination
     *
     * @var string
     */
    private string $entityClass;
    
    /**
     * Le nombre d'enregistrement à récupérer
     *
     * @var integer
     */
    private int $limit = 10;

    /**
     * La page courante
     *
     * @var integer
     */
    private int $currentPage = 1;

    /**
     * Le manager de Doctrine qui nous permet notamment de trouver le repository 
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * Le moteur de template Twig qui va permettre de générer le rendu de la pagination
     *
     * @var Environment
     */
    private Environment $twig;

    /**
     * Le nom de la route que l'on veut utiliser pour les boutons de navigations
     *
     * @var string
     */
    private string $route;

    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var string
     */
    private string $templatePath;

    /**
     * Un tableau pour ordonner les résultats
     *
     * @var array|null
     */
    private ?array $order = null;

    /**
     * Un tableau pour spécifier les critères de filtrage
     *
     * @var array
     */
    private array $criteria = [];

    /**
     * Constructeur du service de pagination qui sera appelé par Symfony
     * 
     * N'oubliez pas de configurer votre fichier service.yaml afin que Symfony sache quelle valeur utiliser 
     * pour le $templatePath
     *
     * @param EntityManagerInterface $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, string $templatePath)
    {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     * Permet de spécifier l'entité sur laquelle on souhaite paginer
     *
     * @param string $entityClass
     * @return self
     */
    public function setEntityClass(string $entityClass): self
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * Permet de récupérer l'entité sur laquelle on est en train de paginer
     *
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * Permet de spécifier le nombre d'enregistrement que l'on souhaite obtenir
     *
     * @param int $limit
     * @return self
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Permet de récupérer le nombre d'enregistrement qui seront renvoyés
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Permet de spécifier l'ordre que l'on souhaite afficher pour les résultats
     *
     * @param array $myOrder
     * @return self
     */
    public function setOrder(array $myOrder): self
    {
        $this->order = $myOrder;
        return $this;
    }

    /**
     * Permet de récupérer le tableau des order
     *
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * Permet de spécifier la page que l'on souhaite afficher
     *
     * @param int $page
     * @return self
     */
    public function setPage(int $page): self
    {
        $this->currentPage = $page;
        return $this;
    }

    /**
     * Permet de récupérer la page qui est actuellement affichée
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Permet de spécifier les critères de filtrage
     *
     * @param array $criteria
     * @return self
     */
    public function setCriteria(array $criteria): self
    {
        $this->criteria = $criteria;
        return $this;
    }

    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     * @throws \Exception si la propriété $entityClass n'est pas configurée
     * @return array
     */
    public function getData(): array
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer! Utilisez la méthode setEntityClass() de votre objet PaginationService");
        }

        $offset = ($this->currentPage - 1) * $this->limit;

        $queryBuilder = $this->manager
            ->getRepository($this->entityClass)
            ->createQueryBuilder('e');

        foreach ($this->criteria as $field => $value) {
            if (strpos($field, '.') !== false) {
                list($relation, $relatedField) = explode('.', $field);
                $queryBuilder->join("e.$relation", 'r')
                            ->andWhere("r.$relatedField = :$relatedField")
                            ->setParameter($relatedField, $value);
            } else {
                $queryBuilder->andWhere("e.$field = :$field")
                            ->setParameter($field, $value);
            }
        }

        if ($this->order) {
            foreach ($this->order as $sort => $order) {
                $queryBuilder->addOrderBy("e.$sort", $order);
            }
        }

        return $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($this->limit)
            ->getQuery()
            ->getResult();
    }



    /**
     * Permet de récupérer le nombre de pages qui existent pour une entité particulière
     *
     * @throws \Exception si la propriété $entityClass n'est pas configurée
     * @return int
     */
    public function getPages(): int
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer! Utilisez la méthode setEntityClass() de votre objet PaginationService");
        }

        $queryBuilder = $this->manager
            ->getRepository($this->entityClass)
            ->createQueryBuilder('e')
            ->select('COUNT(e.id)');

        foreach ($this->criteria as $field => $value) {
            if (strpos($field, '.') !== false) {
                list($relation, $relatedField) = explode('.', $field);
                $queryBuilder->join("e.$relation", 'r')
                            ->andWhere("r.$relatedField = :$relatedField")
                            ->setParameter($relatedField, $value);
            } else {
                $queryBuilder->andWhere("e.$field = :$field")
                            ->setParameter($field, $value);
            }
        }

        $total = $queryBuilder->getQuery()->getSingleScalarResult();

        return ceil($total / $this->limit);
    }
    


    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template Twig
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin de notre propriété
     * $templatePath, en lui passant les variables page, pages et route 
     *
     * @return void
     */
    public function display(): void
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    /**
     * Permet de choisir un template de pagination
     *
     * @param string $templatePath
     * @return self
     */
    public function setTemplatePath(string $templatePath): self
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * Permet de récupérer le templatePath actuellement utilisé
     *
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * Permet de changer la route par défaut pour les liens de la navigation
     *
     * @param string $route
     * @return self
     */
    public function setRoute(string $route): self
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Permet de récupérer le nom de la route qui sera utilisée sur les liens de la pagination
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }
}
