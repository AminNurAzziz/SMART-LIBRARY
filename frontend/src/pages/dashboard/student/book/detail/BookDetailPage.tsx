import { Helmet } from 'react-helmet-async';
import { ReactNode, useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
// @mui
import { alpha } from '@mui/material/styles';
import { Box, Tab, Tabs, Card, Grid, Divider, Container, Typography, Stack } from '@mui/material';
// routes
// @types
import { IProduct } from '@/@types/product';
// components
import Iconify from '@/components/iconify';
import Markdown from '@/components/markdown';
import { useSettingsContext } from '@/components/settings';
import { SkeletonProductDetails } from '@/components/skeleton';
// sections
import { BooksDetailsSummary, BookDetailsCarousel } from '@/sections/dashboard/student/book-detail';

// ----------------------------------------------------------------------

const SUMMARY = [
  {
    title: '100% Original',
    description: 'Chocolate bar candy canes ice cream toffee cookie halvah.',
    icon: 'ic:round-verified',
  },
  {
    title: '10 Day Replacement',
    description: 'Marshmallow biscuit donut dragée fruitcake wafer.',
    icon: 'eva:clock-fill',
  },
  {
    title: 'Year Warranty',
    description: 'Cotton candy gingerbread cake I love sugar sweet.',
    icon: 'ic:round-verified-user',
  },
];

// ----------------------------------------------------------------------

export default function EcommerceProductDetailsPage() {
  const { themeStretch } = useSettingsContext();

  const { name } = useParams();

  const product: IProduct = {
    id: '1',
    cover: 'https://placehold.co/400',
    images: ['https://placehold.co/400', 'https://placehold.co/400'],
    name: 'Star Wars',
    price: 99.99,
    code: 'ABC123',
    sku: 'SKU123',
    tags: ['tag1', 'tag2'],
    priceSale: null,
    totalRating: 4.5,
    totalReview: 100,
    ratings: [
      { name: 'User1', starCount: 5, reviewCount: 50 },
      { name: 'User2', starCount: 4, reviewCount: 30 },
    ],
    reviews: [
      {
        id: '1',
        rating: 5,
        comment: 'Great product!',
        name: '',
        avatarUrl: '',
        isPurchased: false,
        helpful: 0,
        postedAt: '',
      },
      {
        id: '2',
        rating: 4,
        comment: 'Good quality.',
        name: '',
        avatarUrl: '',
        isPurchased: false,
        helpful: 0,
        postedAt: '',
      },
    ],
    colors: ['Red', 'Blue'],
    status: 'Active',
    inventoryType: 'Warehouse',
    sizes: ['Small', 'Medium', 'Large'],
    available: 100,
    description:
      'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Hic ad id illo quaerat eligendi harum, esse a repellendus asperiores neque dignissimos maxime expedita. Dicta minus exercitationem possimus at a ab rem reiciendis consequuntur reprehenderit in sed quam ipsa consequatur maxime nostrum cumque quasi, porro necessitatibus placeat perferendis eos ad eum?',
    sold: 50,
    createdAt: new Date(),
    category: 'Electronics',
    gender: 'Unisex',
  };
  const [currentTab, setCurrentTab] = useState('description');

  const TABS = [
    {
      value: 'description',
      label: 'description',
      component: product ? <Markdown children={product?.description} /> : null,
    },
  ];

  return (
    <>
      <Helmet>
        <title>Book Details</title>
      </Helmet>

      <Container maxWidth={themeStretch ? false : 'lg'}>
        {product && (
          <>
            <Grid container spacing={3}>
              <Grid item xs={12} md={6} lg={7}>
                <BookDetailsCarousel product={product} />
              </Grid>

              <Grid item xs={12} md={6} lg={5}>
                <BooksDetailsSummary product={product} />
              </Grid>
            </Grid>

            <Card sx={{ mt: 4 }}>
              <Tabs
                value={currentTab}
                onChange={(event, newValue) => setCurrentTab(newValue)}
                sx={{ px: 3, bgcolor: 'background.neutral' }}
              >
                {TABS.map((tab) => (
                  <Tab key={tab.value} value={tab.value} label={tab.label} />
                ))}
              </Tabs>

              <Divider />

              {TABS.map(
                (tab) =>
                  tab.value === currentTab && (
                    <Box
                      key={tab.value}
                      sx={{
                        ...(currentTab === 'description' && {
                          p: 3,
                        }),
                      }}
                    >
                      {tab.component}
                    </Box>
                  )
              )}
            </Card>
          </>
        )}

        {/* {isLoading && <SkeletonProductDetails />} */}
      </Container>
    </>
  );
}
